<?php

namespace App\Http\Controllers;

use App\Models\AlternativeProduct;
use App\Models\FamilyMember;
use App\Models\Product;
use App\Models\ScanHistory;
use App\Services\AllergyChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScanController extends Controller
{
    public function dashboard()
    {
        $members = auth()->user()->familyMembers()->with('budget')->get();
        $activeMember = $this->getActiveMember(false);
        $history = ScanHistory::with(['product', 'member'])
            ->where('user_id', auth()->id())
            ->latest('scan_date')
            ->get()
            ->unique('product_id')
            ->take(3)
            ->values();

        return view('dashboard.index', [
            'members' => $members,
            'activeMember' => $activeMember,
            'history' => $history,
            'globalCartCount' => famshopCartCount(),
        ]);
    }

    public function selectMember(Request $request)
    {
        $validated = $request->validate([
            'member_id' => ['required', 'integer'],
        ]);

        FamilyMember::where('user_id', auth()->id())->findOrFail($validated['member_id']);
        session(['active_member_id' => $validated['member_id']]);

        return back()->with('success', 'Active family member updated.');
    }

    public function index()
    {
        return view('scan.index', [
            'activeMember' => $this->getActiveMember(false),
        ]);
    }

    public function scan(Request $request)
    {
        $validated = $request->validate([
            'barcode' => ['required', 'string', 'max:50'],
        ]);

        $member = $this->getActiveMember();
        $product = Product::where('barcode', $validated['barcode'])->first();

        if (! $product) {
            $product = $this->fetchFromOpenFoodFacts($validated['barcode']);
            if (! $product) {
                return back()->with('error', 'Product was not found in the local database or API.');
            }
        }

        $checker = new AllergyChecker();
        $allergyResult = $checker->check($product, $member);
        $budgetResult = app(BudgetController::class)->checkBudget((float) $product->price, $member->budget);

        $safe = $allergyResult['safe'];
        $withinBudget = $budgetResult['within_budget'];

        $matchStatus = match (true) {
            $safe && $withinBudget => 'safe',
            ! $safe && $withinBudget => 'unsafe',
            $safe && ! $withinBudget => 'over_budget',
            default => 'unsafe_over_budget',
        };

        $reasons = [];
        foreach ($allergyResult['triggered_allergens'] as $allergen) {
            $reasons[] = "Contains {$allergen}";
        }
        if (! $withinBudget) {
            $reasons[] = "Exceeds {$budgetResult['exceeded_period']}";
        }

        ScanHistory::create([
            'user_id' => auth()->id(),
            'member_id' => $member->id,
            'product_id' => $product->id,
            'match_status' => $matchStatus,
            'reason' => implode(' | ', $reasons) ?: null,
            'scan_date' => now(),
        ]);

        $alternatives = collect();
        if ($matchStatus !== 'safe') {
            $alternatives = Product::whereIn(
                'id',
                AlternativeProduct::where('product_id', $product->id)->pluck('alternative_product_id')
            )->get();
        }

        $similarProducts = Product::query()
            ->where('id', '!=', $product->id)
            ->when(filled($product->brand), function ($query) use ($product) {
                $query->where('brand', $product->brand);
            }, function ($query) use ($product) {
                $query->where('pr_name', 'like', '%' . str($product->pr_name)->before(' ')->value() . '%');
            })
            ->latest()
            ->take(8)
            ->get();

        return view('scan.result', compact(
            'product',
            'member',
            'allergyResult',
            'budgetResult',
            'matchStatus',
            'alternatives',
            'similarProducts'
        ));
    }

    public function history()
    {
        $records = ScanHistory::with(['product', 'member'])
            ->where('user_id', auth()->id())
            ->latest('scan_date')
            ->paginate(10);

        return view('scan.history', [
            'records' => $records,
        ]);
    }

    public function deleteRecord($id)
    {
        ScanHistory::where('user_id', auth()->id())->findOrFail($id)->delete();

        return back()->with('success', 'Scan record deleted.');
    }

    public function rescan($id)
    {
        $record = ScanHistory::with('product')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        request()->merge(['barcode' => $record->product->barcode]);

        return $this->scan(request());
    }

    private function fetchFromOpenFoodFacts(string $barcode): ?Product
    {
        $response = Http::timeout(5)
            ->get("https://world.openfoodfacts.org/api/v0/product/{$barcode}.json");

        if (! $response->ok() || $response->json('status') !== 1) {
            return null;
        }

        $data = $response->json('product');

        return Product::updateOrCreate(
            ['barcode' => $barcode],
            [
                'pr_name' => $data['product_name'] ?? 'Unknown product',
                'brand' => $data['brands'] ?? null,
                'image_url' => $data['image_url'] ?? null,
                'raw_ingredients' => $data['ingredients_text'] ?? null,
                'halal_status' => 'unknown',
            ]
        );
    }

    private function getActiveMember(bool $failIfMissing = true): ?FamilyMember
    {
        $memberId = session('active_member_id');

        if (! $memberId) {
            if ($failIfMissing) {
                abort(422, 'Please select a family member first.');
            }

            return null;
        }

        return FamilyMember::with(['allergyProfiles', 'budget'])
            ->where('user_id', auth()->id())
            ->findOrFail($memberId);
    }
}

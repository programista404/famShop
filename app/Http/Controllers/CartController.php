<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Services\AllergyChecker;
use App\Models\Product;
use App\Models\ShoppingCart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $activeMemberId = session('active_member_id');
        
        // If no active member selected, show empty cart
        if (!$activeMemberId) {
            return view('cart.index', [
                'cart' => null,
                'itemChecks' => [],
                'safeCount' => 0,
                'unsafeCount' => 0,
                'noMemberSelected' => true,
            ]);
        }
        
        // Load cart for the specific active member
        $cart = ShoppingCart::with('items.product')
            ->with('member.budget', 'member.allergyProfiles')
            ->where('user_id', auth()->id())
            ->where('member_id', $activeMemberId)
            ->whereNull('purchase_date')
            ->first();

        $itemChecks = [];
        $unsafeCount = 0;
        $safeCount = 0;

        if ($cart?->member && $cart->items) {
            $checker = new AllergyChecker();

            foreach ($cart->items as $item) {
                if (! $item->product) {
                    continue;
                }

                $allergyResult = $checker->check($item->product, $cart->member);
                $budgetResult = app(BudgetController::class)->checkBudget((float) $item->product->price, $cart->member->budget);
                $safe = $allergyResult['safe'] && $budgetResult['within_budget'];
                $reasons = [];

                if (! $allergyResult['safe']) {
                    $reasons[] = 'Contains ' . implode(', ', $allergyResult['triggered_allergens']);
                }

                if (! $budgetResult['within_budget']) {
                    $reasons[] = 'Exceeds ' . $budgetResult['exceeded_period'];
                }

                $itemChecks[$item->id] = [
                    'safe' => $safe,
                    'status' => $safe ? 'Safe' : 'Warning',
                    'message' => $safe
                        ? 'Safe for ' . $cart->member->name_member
                        : implode(' • ', $reasons),
                ];

                $safe ? $safeCount++ : $unsafeCount++;
            }
        }

        return view('cart.index', [
            'cart' => $cart,
            'itemChecks' => $itemChecks,
            'safeCount' => $safeCount,
            'unsafeCount' => $unsafeCount,
        ]);
    }

    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $memberId = session('active_member_id');
        if (!$memberId) {
            return back()->with('error', 'Please select a family member first.');
        }
        
        $product = Product::findOrFail($validated['product_id']);
        $qty = $validated['quantity'] ?? 1;

        // Find or create cart for the specific member
        $cart = ShoppingCart::firstOrCreate(
            ['user_id' => auth()->id(), 'member_id' => $memberId, 'purchase_date' => null],
            ['total_cost' => 0]
        );

        $item = $cart->items()->where('product_id', $product->id)->first();
        if ($item) {
            $newQty = $item->quantity + $qty;
            $item->update([
                'quantity' => $newQty,
                'total_price' => $newQty * (float) $product->price,
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $qty,
                'total_price' => $qty * (float) $product->price,
            ]);
        }

        $cart->update([
            'total_cost' => $cart->items()->sum('total_price'),
        ]);

        $redirectTo = $request->input('redirect_to', '/cart');
        if (! is_string($redirectTo) || ! str_starts_with($redirectTo, '/')) {
            $redirectTo = '/cart';
        }

        return redirect($redirectTo)->with('success', 'Product added to cart.');
    }

    public function updateQty(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $item = CartItem::whereHas('cart', function ($query) {
            $query->where('user_id', auth()->id())->whereNull('purchase_date');
        })->with('product', 'cart')->findOrFail($id);

        $item->update([
            'quantity' => $validated['quantity'],
            'total_price' => $validated['quantity'] * (float) $item->product->price,
        ]);

        $item->cart->update([
            'total_cost' => $item->cart->items()->sum('total_price'),
        ]);

        return back()->with('success', 'Quantity updated.');
    }

    public function removeItem($id)
    {
        $item = CartItem::whereHas('cart', function ($query) {
            $query->where('user_id', auth()->id())->whereNull('purchase_date');
        })->with('cart')->findOrFail($id);

        $cart = $item->cart;
        $item->delete();

        $cart->update([
            'total_cost' => $cart->items()->sum('total_price'),
        ]);

        return back()->with('success', 'Product removed from cart.');
    }

    public function checkout(Request $request)
    {
        return redirect('/cart/payment');
    }

    public function payment()
    {
        $activeMemberId = session('active_member_id');
        
        if (!$activeMemberId) {
            return back()->with('error', 'Please select a family member first.');
        }
        
        // Load cart for the specific active member
        $cart = ShoppingCart::with('items.product')
            ->with('member')
            ->where('user_id', auth()->id())
            ->where('member_id', $activeMemberId)
            ->whereNull('purchase_date')
            ->firstOrFail();

        return view('cart.payment', [
            'cart' => $cart,
            'total' => $cart->items->sum('total_price'),
        ]);
    }

    public function paymentDone()
    {
        $activeMemberId = session('active_member_id');
        
        if (!$activeMemberId) {
            return back()->with('error', 'Please select a family member first.');
        }
        
        // Get cart for the specific active member
        $cart = ShoppingCart::query()
            ->where('user_id', auth()->id())
            ->where('member_id', $activeMemberId)
            ->whereNull('purchase_date')
            ->firstOrFail();

        $cart->items()->delete();
        $cart->update([
            'total_cost' => 0,
        ]);

        return redirect('/dashboard')->with('success', 'Demo checkout completed. Your cart was cleared.');
    }

    public function clearCart()
    {
        $activeMemberId = session('active_member_id');
        
        if (!$activeMemberId) {
            return back()->with('error', 'Please select a family member first.');
        }
        
        // Get cart for the specific active member
        $cart = ShoppingCart::where('user_id', auth()->id())
            ->where('member_id', $activeMemberId)
            ->whereNull('purchase_date')
            ->first();

        if ($cart) {
            $cart->items()->delete();
            $cart->update(['total_cost' => 0]);
            
            return back()->with('success', 'Cart cleared successfully.');
        }

        return back()->with('info', 'Cart is already empty.');
    }
}

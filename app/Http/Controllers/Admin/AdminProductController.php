<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminProductController extends Controller
{
    public function index()
    {
        return view('admin.products', [
            'products' => Product::with('ingredients')->latest()->get(),
            'ingredients' => Ingredient::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        $product = Product::create($validated + ['halal_status' => $validated['halal_status'] ?? 'unknown']);
        $this->syncIngredients($product, $request);

        return back()->with('success', 'Product created successfully.');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $validated = $this->validateProduct($request, $product->id);

        $product->update($validated);
        $this->syncIngredients($product, $request);

        return back()->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return back()->with('success', 'Product deleted successfully.');
    }

    private function validateProduct(Request $request, ?int $productId = null): array
    {
        return $request->validate([
            'pr_name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'barcode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'barcode')->ignore($productId),
            ],
            'price' => ['nullable', 'numeric', 'min:0'],
            'image_url' => ['nullable', 'url', 'max:1000'],
            'halal_status' => ['nullable', 'in:halal,haram,unknown'],
            'raw_ingredients' => ['nullable', 'string'],
            'ingredient_ids' => ['nullable', 'array'],
            'ingredient_ids.*' => ['integer', 'exists:ingredients,id'],
        ]);
    }

    private function syncIngredients(Product $product, Request $request): void
    {
        $ingredientIds = collect($request->input('ingredient_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $pivotData = [];
        foreach ($ingredientIds as $ingredientId) {
            $pivotData[$ingredientId] = [
                'note' => $request->input("ingredient_notes.{$ingredientId}") ?: null,
            ];
        }

        $product->ingredients()->sync($pivotData);
    }
}

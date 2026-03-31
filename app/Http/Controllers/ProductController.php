<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private array $categories = [
        ['key' => 'dairy_free', 'label' => 'Dairy Free', 'icon' => 'bi-cup-straw'],
        ['key' => 'vegan', 'label' => 'Vegan', 'icon' => 'bi-tree-fill'],
        ['key' => 'low_sugar', 'label' => 'Low Sugar', 'icon' => 'bi-heart-pulse-fill'],
        ['key' => 'drinks', 'label' => 'Drinks', 'icon' => 'bi-cup-hot-fill'],
        ['key' => 'snacks', 'label' => 'Snacks', 'icon' => 'bi-box2-fill'],
        ['key' => 'fresh', 'label' => 'Fresh', 'icon' => 'bi-basket2-fill'],
    ];

    public function explore(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $category = (string) $request->query('category', '');

        $products = Product::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('pr_name', 'like', '%' . $search . '%')
                        ->orWhere('brand', 'like', '%' . $search . '%')
                        ->orWhere('raw_ingredients', 'like', '%' . $search . '%');
                });
            })
            ->get()
            ->map(function (Product $product) {
                $product->explore_categories = $this->detectCategories($product);
                return $product;
            });

        if ($category !== '') {
            $products = $products->filter(function (Product $product) use ($category) {
                return in_array($category, $product->explore_categories, true);
            })->values();
        }

        return view('products.explore', [
            'products' => $products,
            'categories' => $this->categories,
            'activeCategory' => $category,
            'search' => $search,
        ]);
    }

    public function show($id)
    {
        $product = Product::query()->findOrFail($id);
        $product->explore_categories = $this->detectCategories($product);

        $similarProducts = Product::query()
            ->where('id', '!=', $product->id)
            ->when(filled($product->brand), function ($query) use ($product) {
                $query->where('brand', $product->brand);
            }, function ($query) use ($product) {
                $keyword = trim((string) str($product->pr_name)->before(' '));

                if ($keyword !== '') {
                    $query->where('pr_name', 'like', '%' . $keyword . '%');
                }
            })
            ->latest()
            ->take(8)
            ->get()
            ->map(function (Product $similarProduct) {
                $similarProduct->explore_categories = $this->detectCategories($similarProduct);
                return $similarProduct;
            });

        return view('products.show', [
            'product' => $product,
            'categories' => $this->categories,
            'similarProducts' => $similarProducts,
        ]);
    }

    private function detectCategories(Product $product): array
    {
        $haystack = strtolower(trim(
            implode(' ', [
                $product->pr_name ?? '',
                $product->brand ?? '',
                $product->raw_ingredients ?? '',
            ])
        ));

        $categories = [];

        if (! preg_match('/milk|cheese|cream|butter|whey|lactose/', $haystack)) {
            $categories[] = 'dairy_free';
        }

        if (! preg_match('/milk|cheese|cream|butter|whey|egg|meat|pork|chicken|beef|gelatin/', $haystack)) {
            $categories[] = 'vegan';
        }

        if (! preg_match('/sugar|syrup|fructose|glucose/', $haystack)) {
            $categories[] = 'low_sugar';
        }

        if (preg_match('/juice|drink|water|milk|coffee|tea|soda/', $haystack)) {
            $categories[] = 'drinks';
        }

        if (preg_match('/chips|cracker|biscuit|cookie|bar|snack|nuts/', $haystack)) {
            $categories[] = 'snacks';
        }

        if (preg_match('/fruit|vegetable|fresh|apple|banana|berry|salad/', $haystack)) {
            $categories[] = 'fresh';
        }

        if ($categories === []) {
            $categories[] = 'snacks';
        }

        return array_values(array_unique($categories));
    }
}

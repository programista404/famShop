<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ShoppingCart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = ShoppingCart::with('items.product')
            ->with('member.budget')
            ->where('user_id', auth()->id())
            ->whereNull('purchase_date')
            ->first();

        return view('cart.index', [
            'cart' => $cart,
        ]);
    }

    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $memberId = session('active_member_id');
        $product = Product::findOrFail($validated['product_id']);
        $qty = $validated['quantity'] ?? 1;

        $cart = ShoppingCart::firstOrCreate(
            ['user_id' => auth()->id(), 'purchase_date' => null],
            ['member_id' => $memberId, 'total_cost' => 0]
        );

        if ($memberId && ! $cart->member_id) {
            $cart->update(['member_id' => $memberId]);
        }

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

        return back()->with('success', 'Product added to cart.');
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
        ShoppingCart::query()
            ->where('user_id', auth()->id())
            ->whereNull('purchase_date')
            ->firstOrFail();

        return redirect('/cart/payment');
    }

    public function payment()
    {
        $cart = ShoppingCart::with('items.product')
            ->with('member')
            ->where('user_id', auth()->id())
            ->whereNull('purchase_date')
            ->firstOrFail();

        return view('cart.payment', [
            'cart' => $cart,
            'total' => $cart->items->sum('total_price'),
        ]);
    }

    public function paymentDone()
    {
        $cart = ShoppingCart::query()
            ->where('user_id', auth()->id())
            ->whereNull('purchase_date')
            ->firstOrFail();

        $cart->items()->delete();
        $cart->update([
            'total_cost' => 0,
        ]);

        return redirect('/dashboard')->with('success', 'Demo checkout completed. Your cart was cleared.');
    }
}

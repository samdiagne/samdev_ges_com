<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class PanierController extends Controller
{
    public function index()
    {
        $cart     = session('cart', []);
        $total    = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $settings = \App\Models\CompanySetting::getSettings();

        return view('shop.panier', compact('cart', 'total', 'settings'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock_quantity <= 0) {
            return back()->with('error', 'Ce produit est en rupture de stock.');
        }

        $cart = session('cart', []);
        $key  = 'product_' . $product->id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $request->quantity;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => $product->selling_price,
                'image'      => $product->main_image,
                'slug'       => $product->slug,
                'quantity'   => $request->quantity,
                'stock'      => $product->stock_quantity,
            ];
        }

        // Limiter au stock disponible
        if ($cart[$key]['quantity'] > $product->stock_quantity) {
            $cart[$key]['quantity'] = $product->stock_quantity;
        }

        session(['cart' => $cart]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'count' => collect(session('cart'))->sum('quantity')]);
        }

        return back()->with('success', $product->name . ' ajouté au panier !');
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity'   => 'required|integer|min:0',
        ]);

        $cart = session('cart', []);
        $key  = 'product_' . $request->product_id;

        if ($request->quantity <= 0) {
            unset($cart[$key]);
        } else {
            $cart[$key]['quantity'] = $request->quantity;
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Panier mis à jour !');
    }

    public function remove(Request $request)
    {
        $cart = session('cart', []);
        $key  = 'product_' . $request->product_id;
        unset($cart[$key]);
        session(['cart' => $cart]);

        return back()->with('success', 'Produit retiré du panier.');
    }
}
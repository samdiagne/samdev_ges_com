<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\CompanySetting;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)
            ->withCount('activeProducts')
            ->having('active_products_count', '>', 0)
            ->get();

        $settings = CompanySetting::getSettings();

        return view('shop.catalogue', compact('categories', 'settings'));
    }

    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'images'])
            ->firstOrFail();

        $related = Product::where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('stock_quantity', '>', 0)
            ->limit(4)
            ->get();

        $settings = CompanySetting::getSettings();

        return view('shop.produit', compact('product', 'related', 'settings'));
    }
}
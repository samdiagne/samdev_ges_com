<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\CompanySetting;

class HomeController extends Controller
{
    public function index()
    {
        $featured = Product::where('is_active', true)
            ->where('is_featured', true)
            ->where('stock_quantity', '>', 0)
            ->with('category')
            ->limit(8)
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount(['activeProducts'])
            ->having('active_products_count', '>', 0)
            ->get();

        $newArrivals = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->with('category')
            ->latest()
            ->limit(8)
            ->get();

        $settings = CompanySetting::getSettings();

        return view('shop.home', compact(
            'featured', 'categories', 'newArrivals', 'settings'
        ));
    }
}
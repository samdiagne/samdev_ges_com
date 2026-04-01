<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $products   = Product::where('is_active', true)
            ->select('slug', 'updated_at')->latest()->get();

        $categories = Category::where('is_active', true)
            ->select('slug', 'updated_at')->get();

        $content = view('sitemap', compact('products', 'categories'));

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }
}
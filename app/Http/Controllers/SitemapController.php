<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Categorie;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $produits = Produit::where('est_actif', true)
            ->select('slug', 'updated_at')
            ->latest()
            ->get();

        $categories = Categorie::where('est_active', true)
            ->select('slug', 'updated_at')
            ->get();

        $content = view('sitemap', compact('produits', 'categories'));

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }
}

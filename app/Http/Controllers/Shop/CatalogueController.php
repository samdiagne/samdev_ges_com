<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use App\Models\Categorie;
use App\Models\Parametre;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    public function index(Request $request)
    {
        $categories = Categorie::where('est_active', true)
            ->withCount('produitsActifs')
            ->having('produits_actifs_count', '>', 0)
            ->orderBy('ordre')
            ->get();

        $parametres = Parametre::first();

        return view('shop.catalogue', compact('categories', 'parametres'));
    }

    public function show(string $slug)
    {
        $plat = Produit::where('slug', $slug)
            ->where('est_actif', true)
            ->with(['categorie', 'images'])
            ->firstOrFail();

        $similaires = Produit::where('est_actif', true)
            ->where('categorie_id', $plat->categorie_id)
            ->where('id', '!=', $plat->id)
            ->with('avisApprouves')
            ->limit(4)
            ->get();

        $parametres = Parametre::first();

        return view('shop.plat', compact('plat', 'similaires', 'parametres'));
    }
}

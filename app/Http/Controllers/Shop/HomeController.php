<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use App\Models\Categorie;
use App\Models\Parametre;

class HomeController extends Controller
{
    public function index()
    {
        $misEnAvant = Produit::where('est_actif', true)
            ->where('est_mis_en_avant', true)
            ->whereDoesntHave('categorie', fn($q) => $q->where('slug', 'supplements')->orWhere('id', 27))
            ->with(['categorie', 'avisApprouves'])
            ->limit(8)
            ->get();

        $categories = Categorie::where('est_active', true)
            ->where('slug', '!=', 'supplements')
            ->where('id', '!=', 27)
            ->withCount(['produitsActifs'])
            ->having('produits_actifs_count', '>', 0)
            ->orderBy('ordre')
            ->get();

        $nouveautes = Produit::where('est_actif', true)
            ->whereDoesntHave('categorie', fn($q) => $q->where('slug', 'supplements')->orWhere('id', 27))
            ->with(['categorie', 'avisApprouves'])
            ->latest()
            ->limit(8)
            ->get();

        $platCarousel = Produit::where('est_actif', true)
            ->whereDoesntHave('categorie', fn($q) => $q->where('slug', 'supplements')->orWhere('id', 27))
            ->with(['categorie', 'avisApprouves'])
            ->inRandomOrder()
            ->limit(8)
            ->get();

        $sectionsCategories = Categorie::where('est_active', true)
            ->where('slug', '!=', 'supplements')
            ->where('id', '!=', 27)
            ->orderBy('ordre')
            ->get()
            ->map(fn($cat) => [
                'categorie' => $cat,
                'produits'  => Produit::where('est_actif', true)
                    ->where('categorie_id', $cat->id)
                    ->with('avisApprouves')
                    ->limit(8)
                    ->get(),
            ])
            ->filter(fn($s) => $s['produits']->isNotEmpty())
            ->values();

        $parametres = Parametre::first();

        return view('shop.home', compact(
            'misEnAvant', 'categories', 'nouveautes', 'sectionsCategories', 'platCarousel', 'parametres'
        ));
    }
}

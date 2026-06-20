<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Plat;
use App\Models\Categorie;
use App\Models\Parametre;

class HomeController extends Controller
{
    public function index()
    {
        $misEnAvant = Plat::where('est_actif', true)
            ->where('est_mis_en_avant', true)
            ->where(fn($q) => $q->where('stock', '>', 0)->orWhere('sur_commande', true))
            ->with(['categorie', 'avisApprouves'])
            ->limit(8)
            ->get();

        $categories = Categorie::where('est_active', true)
            ->withCount(['platsActifs'])
            ->having('plats_actifs_count', '>', 0)
            ->orderBy('ordre')
            ->get();

        $nouveautes = Plat::where('est_actif', true)
            ->where(fn($q) => $q->where('stock', '>', 0)->orWhere('sur_commande', true))
            ->with(['categorie', 'avisApprouves'])
            ->latest()
            ->limit(8)
            ->get();

        $platCarousel = Plat::where('est_actif', true)
            ->where(fn($q) => $q->where('stock', '>', 0)->orWhere('sur_commande', true))
            ->with('categorie')
            ->inRandomOrder()
            ->limit(16)
            ->get();

        $sectionsCategories = Categorie::where('est_active', true)
            ->orderBy('ordre')
            ->get()
            ->map(fn($cat) => [
                'categorie' => $cat,
                'plats'     => Plat::where('est_actif', true)
                    ->where('categorie_id', $cat->id)
                    ->where(fn($q) => $q->where('stock', '>', 0)->orWhere('sur_commande', true))
                    ->with('avisApprouves')
                    ->limit(8)
                    ->get(),
            ])
            ->filter(fn($s) => $s['plats']->isNotEmpty())
            ->values();

        $parametres = Parametre::first();

        return view('shop.home', compact(
            'misEnAvant', 'categories', 'nouveautes', 'sectionsCategories', 'platCarousel', 'parametres'
        ));
    }
}

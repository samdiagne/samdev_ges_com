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
            ->where('stock', '>', 0)
            ->with('categorie')
            ->limit(8)
            ->get();

        $categories = Categorie::where('est_active', true)
            ->withCount(['platsActifs'])
            ->having('plats_actifs_count', '>', 0)
            ->orderBy('ordre')
            ->get();

        $nouveautes = Plat::where('est_actif', true)
            ->where('stock', '>', 0)
            ->with('categorie')
            ->latest()
            ->limit(8)
            ->get();

        $parametres = Parametre::first();

        return view('shop.home', compact(
            'misEnAvant', 'categories', 'nouveautes', 'parametres'
        ));
    }
}

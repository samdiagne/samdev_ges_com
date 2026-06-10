<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Plat;
use App\Models\Parametre;
use Illuminate\Http\Request;

class PanierController extends Controller
{
    public function index()
    {
        $panier     = session('panier', []);
        $total      = collect($panier)->sum(fn($i) => $i['prix'] * $i['quantite']);
        $parametres = Parametre::first();

        return view('shop.panier', compact('panier', 'total', 'parametres'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'plat_id'  => 'required|exists:plats,id',
            'quantite' => 'required|integer|min:1',
        ]);

        $plat = Plat::findOrFail($request->plat_id);

        if ($plat->stock <= 0) {
            return back()->with('error', 'Ce plat est en rupture de stock.');
        }

        $panier = session('panier', []);
        $cle    = 'plat_' . $plat->id;

        if (isset($panier[$cle])) {
            $panier[$cle]['quantite'] += $request->quantite;
        } else {
            $panier[$cle] = [
                'plat_id'  => $plat->id,
                'nom'      => $plat->nom,
                'prix'     => $plat->prix_vente,
                'image'    => $plat->image_principale,
                'slug'     => $plat->slug,
                'quantite' => $request->quantite,
                'stock'    => $plat->stock,
            ];
        }

        // Limiter au stock disponible
        if ($panier[$cle]['quantite'] > $plat->stock) {
            $panier[$cle]['quantite'] = $plat->stock;
        }

        session(['panier' => $panier]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'count'   => collect(session('panier'))->sum('quantite'),
            ]);
        }

        return back()->with('success', $plat->nom . ' ajouté au panier !');
    }

    public function update(Request $request)
    {
        $request->validate([
            'plat_id'  => 'required',
            'quantite' => 'required|integer|min:0',
        ]);

        $panier = session('panier', []);
        $cle    = 'plat_' . $request->plat_id;

        if ($request->quantite <= 0) {
            unset($panier[$cle]);
        } else {
            $panier[$cle]['quantite'] = $request->quantite;
        }

        session(['panier' => $panier]);

        return back()->with('success', 'Panier mis à jour !');
    }

    public function remove(Request $request)
    {
        $panier = session('panier', []);
        $cle    = 'plat_' . $request->plat_id;
        unset($panier[$cle]);
        session(['panier' => $panier]);

        return back()->with('success', 'Plat retiré du panier.');
    }
}

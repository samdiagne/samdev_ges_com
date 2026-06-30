<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Produit;
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
            'produit_id' => 'required|exists:produits,id',
            'quantite'   => 'required|integer|min:1',
        ]);

        $plat = Produit::findOrFail($request->produit_id);

        if (! $plat->sur_commande && $plat->stock <= 0) {
            return back()->with('error', 'Ce produit est en rupture de stock.');
        }

        $panier = session('panier', []);
        $cle    = 'produit_' . $plat->id;

        if (isset($panier[$cle])) {
            $panier[$cle]['quantite'] += $request->quantite;
        } else {
            $panier[$cle] = [
                'produit_id' => $plat->id,
                'nom'      => $plat->nom,
                'prix'     => $plat->prix_vente,
                'image'    => $plat->image_principale,
                'slug'     => $plat->slug,
                'quantite' => $request->quantite,
                'stock'    => $plat->stock,
            ];
        }

        if (! $plat->sur_commande && $panier[$cle]['quantite'] > $plat->stock) {
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
            'produit_id' => 'required',
            'quantite'   => 'required|integer|min:0',
        ]);

        $panier = session('panier', []);
        $cle    = 'produit_' . $request->produit_id;

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
        $cle    = 'produit_' . $request->produit_id;
        unset($panier[$cle]);
        session(['panier' => $panier]);

        return back()->with('success', 'Produit retiré du panier.');
    }
}

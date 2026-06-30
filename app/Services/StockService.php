<?php

namespace App\Services;

use App\Models\Produit;
use App\Models\MouvementStock;
use Illuminate\Support\Facades\Auth;

class StockService
{
    public function decrementerStock(
        Produit $plat,
        int     $quantite,
        string  $source,
        ?int    $sourceId = null,
        ?string $motif    = null
    ): void {
        $avant = $plat->stock;
        $apres = $avant - $quantite;

        $plat->update(['stock' => $apres]);

        MouvementStock::create([
            'produit_id'          => $plat->id,
            'user_id'             => Auth::id(),
            'type'                => 'sortie',
            'source'              => $source,
            'source_id'           => $sourceId,
            'stock_avant'         => $avant,
            'quantite_changement' => $quantite,
            'stock_apres'         => $apres,
            'motif'               => $motif ?? 'Vente',
            'effectue_le'         => now(),
        ]);
    }

    public function incrementerStock(
        Produit $plat,
        int     $quantite,
        string  $source,
        ?int    $sourceId = null,
        ?string $motif    = null
    ): void {
        $avant = $plat->stock;
        $apres = $avant + $quantite;

        $plat->update(['stock' => $apres]);

        MouvementStock::create([
            'produit_id'          => $plat->id,
            'user_id'             => Auth::id(),
            'type'                => 'entree',
            'source'              => $source,
            'source_id'           => $sourceId,
            'stock_avant'         => $avant,
            'quantite_changement' => $quantite,
            'stock_apres'         => $apres,
            'motif'               => $motif ?? 'Approvisionnement',
            'effectue_le'         => now(),
        ]);
    }

    public function ajusterStock(
        Produit $plat,
        int     $nouvelleQuantite,
        ?string $motif = null
    ): void {
        $avant    = $plat->stock;
        $diff     = abs($nouvelleQuantite - $avant);
        $type     = $nouvelleQuantite > $avant ? 'entree' : 'sortie';

        $plat->update(['stock' => $nouvelleQuantite]);

        MouvementStock::create([
            'produit_id'          => $plat->id,
            'user_id'             => Auth::id(),
            'type'                => $type,
            'source'              => 'ajustement',
            'source_id'           => null,
            'stock_avant'         => $avant,
            'quantite_changement' => $diff,
            'stock_apres'         => $nouvelleQuantite,
            'motif'               => $motif ?? 'Ajustement manuel',
            'effectue_le'         => now(),
        ]);
    }
}

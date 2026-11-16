<?php

namespace App\Services;

use App\Models\PalierGroupe;
use Illuminate\Support\Collection;

/**
 * CartService — Gestion du panier et des paliers de prix groupés.
 *
 * Un palier de groupe permet d'appliquer un prix dégressif à partir d'un
 * certain nombre d'unités commandées au sein d'une même catégorie, toutes
 * variétés confondues.
 *
 * Exemple : 10 Bissap + 10 Gingembre + 5 Tamarin = 25 bouteilles 250ml
 * → palier 25 bouteilles déclenché → 400 FCFA/unité pour TOUS les jus 250ml.
 */
class CartService
{
    /**
     * Recalcule les prix du panier (session shop) en tenant compte des paliers groupe.
     *
     * @param  array $panier  Le tableau issu de session('panier', [])
     * @return array          Le panier mis à jour avec 'prix' recalculé + méta palier
     */
    public function recalculerPaliersGroupe(array $panier): array
    {
        if (empty($panier)) {
            return $panier;
        }

        // Auto-compléter categorie_id et prix_base si absents
        $produitIdsMissing = [];
        foreach ($panier as $item) {
            if (! isset($item['categorie_id']) || ! isset($item['prix_base'])) {
                if (isset($item['produit_id'])) {
                    $produitIdsMissing[] = $item['produit_id'];
                }
            }
        }
        if (! empty($produitIdsMissing)) {
            $produits = \App\Models\Produit::whereIn('id', array_unique($produitIdsMissing))
                ->get(['id', 'categorie_id', 'prix_vente'])
                ->keyBy('id');
            foreach ($panier as $key => $item) {
                if (isset($item['produit_id']) && $p = $produits->get($item['produit_id'])) {
                    if (! isset($item['categorie_id'])) {
                        $panier[$key]['categorie_id'] = $p->categorie_id;
                    }
                    if (! isset($item['prix_base'])) {
                        $panier[$key]['prix_base'] = (float) $p->prix_vente;
                    }
                }
            }
        }

        // Charger tous les paliers actifs, indexés par categorie_id
        // On garde le palier le plus avantageux (prix le plus bas) par catégorie
        $paliers = PalierGroupe::actifs()
            ->orderBy('prix_unitaire')   // du moins cher au plus cher
            ->get()
            ->groupBy('categorie_id');   // Collection de collections

        // Calculer les totaux de quantité par categorie_id
        $qtesParCategorie = [];
        foreach ($panier as $item) {
            $catId = $item['categorie_id'] ?? null;
            if ($catId === null) continue;
            $qtesParCategorie[$catId] = ($qtesParCategorie[$catId] ?? 0) + (int) ($item['quantite'] ?? 0);
        }

        // Déterminer le palier applicable par catégorie
        $palierApplique = []; // categorie_id => prix_unitaire palier ou null
        foreach ($qtesParCategorie as $catId => $totalQte) {
            if (! isset($paliers[$catId])) continue;

            // Chercher le palier le plus bas dont quantite_min est atteinte
            $meilleur = $paliers[$catId]
                ->filter(fn($p) => $totalQte >= $p->quantite_min)
                ->sortBy('prix_unitaire')
                ->first();

            $palierApplique[$catId] = $meilleur ? (float) $meilleur->prix_unitaire : null;
        }

        // Appliquer les prix recalculés sur chaque item
        foreach ($panier as $key => $item) {
            $catId = $item['categorie_id'] ?? null;
            $prixBase = (float) ($item['prix_base'] ?? $item['prix']);

            if ($catId !== null && isset($palierApplique[$catId]) && $palierApplique[$catId] !== null) {
                // Palier groupe actif → on remplace le prix (hors suppléments)
                $supplement = (float) ($item['prix_supplement'] ?? 0);
                $panier[$key]['prix']                   = $palierApplique[$catId] + $supplement;
                $panier[$key]['palier_groupe_applique']  = true;
                $panier[$key]['palier_groupe_prix']      = $palierApplique[$catId];
            } else {
                // Pas de palier groupe → retour au prix de base
                $supplement = (float) ($item['prix_supplement'] ?? 0);
                $panier[$key]['prix']                   = $prixBase + $supplement;
                $panier[$key]['palier_groupe_applique']  = false;
                $panier[$key]['palier_groupe_prix']      = null;
            }
        }

        return $panier;
    }

    /**
     * Version pour le panier POS (PointOfSale).
     * Le panier POS utilise 'prix_unitaire' + 'prix_base' + 'categorie_id'.
     *
     * @param  array $panier  Tableau de lignes POS
     * @return array          Tableau mis à jour
     */
    public function recalculerPaliersGroupePOS(array $panier): array
    {
        if (empty($panier)) {
            return $panier;
        }

        // Auto-compléter categorie_id et prix_base si absents
        $produitIdsMissing = [];
        foreach ($panier as $item) {
            if ($item['is_menu'] ?? false) continue;
            if (! isset($item['categorie_id']) || ! isset($item['prix_base'])) {
                if (isset($item['produit_id'])) {
                    $produitIdsMissing[] = $item['produit_id'];
                }
            }
        }
        if (! empty($produitIdsMissing)) {
            $produits = \App\Models\Produit::whereIn('id', array_unique($produitIdsMissing))
                ->get(['id', 'categorie_id', 'prix_vente'])
                ->keyBy('id');
            foreach ($panier as $index => $item) {
                if ($item['is_menu'] ?? false) continue;
                if (isset($item['produit_id']) && $p = $produits->get($item['produit_id'])) {
                    if (! isset($item['categorie_id'])) {
                        $panier[$index]['categorie_id'] = $p->categorie_id;
                    }
                    if (! isset($item['prix_base'])) {
                        $panier[$index]['prix_base'] = (float) $p->prix_vente;
                    }
                }
            }
        }

        // Charger les paliers actifs groupés par categorie_id
        $paliers = PalierGroupe::actifs()
            ->orderBy('prix_unitaire')
            ->get()
            ->groupBy('categorie_id');

        // Somme des quantités par catégorie (uniquement lignes non-menu et non-supplément)
        $qtesParCategorie = [];
        foreach ($panier as $item) {
            if ($item['is_menu'] ?? false) continue; // menus ignorés
            $catId = $item['categorie_id'] ?? null;
            if ($catId === null) continue;
            $qtesParCategorie[$catId] = ($qtesParCategorie[$catId] ?? 0) + (int) ($item['quantite'] ?? 0);
        }

        // Meilleur palier par catégorie
        $palierApplique = [];
        foreach ($qtesParCategorie as $catId => $totalQte) {
            if (! isset($paliers[$catId])) continue;
            $meilleur = $paliers[$catId]
                ->filter(fn($p) => $totalQte >= $p->quantite_min)
                ->sortBy('prix_unitaire')
                ->first();
            $palierApplique[$catId] = $meilleur ? (float) $meilleur->prix_unitaire : null;
        }

        // Appliquer
        foreach ($panier as $index => $item) {
            if ($item['is_menu'] ?? false) continue;

            $catId    = $item['categorie_id'] ?? null;
            $prixBase = (float) ($item['prix_base'] ?? $item['prix_unitaire']);

            if ($catId !== null && isset($palierApplique[$catId]) && $palierApplique[$catId] !== null) {
                $panier[$index]['prix_unitaire']          = $palierApplique[$catId];
                $panier[$index]['palier_groupe_applique'] = true;
                $panier[$index]['palier_groupe_prix']     = $palierApplique[$catId];
            } else {
                // Pas de palier groupe — appliquer le palier individuel si existant
                $palierQte  = $item['quantite_palier'] ?? null;
                $palierPrix = $item['prix_palier'] ?? null;
                if ($palierQte && $palierPrix && $item['quantite'] >= $palierQte) {
                    $panier[$index]['prix_unitaire'] = $palierPrix;
                } else {
                    $panier[$index]['prix_unitaire'] = $prixBase;
                }
                $panier[$index]['palier_groupe_applique'] = false;
                $panier[$index]['palier_groupe_prix']     = null;
            }

            // Recalcul du total de la ligne
            $panier[$index]['total'] = round(
                ($panier[$index]['prix_unitaire'] * $item['quantite']) - ($item['remise'] ?? 0), 2
            );
        }

        return $panier;
    }

    /**
     * Retourne les informations de paliers actifs pour l'affichage dans le panier.
     * Utile pour afficher "Plus que X unités pour le palier Y FCFA".
     *
     * @param  array $panier
     * @return array  ['categorie_id' => ['palier' => PalierGroupe, 'actuel' => int, 'manque' => int, 'applique' => bool]]
     */
    public function infoPaliersGroupe(array $panier): array
    {
        $paliers = PalierGroupe::actifs()
            ->with('categorie')
            ->orderBy('quantite_min')
            ->get()
            ->groupBy('categorie_id');

        $qtesParCategorie = [];
        foreach ($panier as $item) {
            $catId = $item['categorie_id'] ?? null;
            if ($catId === null) continue;
            $qtesParCategorie[$catId] = ($qtesParCategorie[$catId] ?? 0) + (int) ($item['quantite'] ?? 0);
        }

        $result = [];
        foreach ($paliers as $catId => $groupePaliers) {
            $totalQte = $qtesParCategorie[$catId] ?? 0;

            // Palier déjà atteint (le plus avantageux)
            $atteint = $groupePaliers
                ->filter(fn($p) => $totalQte >= $p->quantite_min)
                ->sortBy('prix_unitaire')
                ->first();

            // Prochain palier non encore atteint
            $prochain = $groupePaliers
                ->filter(fn($p) => $totalQte < $p->quantite_min)
                ->sortBy('quantite_min')
                ->first();

            $result[$catId] = [
                'actuel'   => $totalQte,
                'applique' => $atteint !== null,
                'palier'   => $atteint ?? $prochain,
                'manque'   => $prochain ? max(0, $prochain->quantite_min - $totalQte) : 0,
                'label'    => ($atteint ?? $prochain)?->label,
            ];
        }

        return $result;
    }
}

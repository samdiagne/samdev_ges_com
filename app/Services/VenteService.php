<?php

namespace App\Services;

use App\Models\Vente;
use App\Models\VenteLigne;
use App\Models\Facture;
use App\Models\Parametre;
use App\Models\Plat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VenteService
{
    public function __construct(private StockService $stockService) {}

    /**
     * Créer une vente (POS) avec ses lignes, sa facture et les mouvements de stock.
     *
     * @param  array   $panier     Tableau de lignes [{plat_id, nom, prix_unitaire, quantite, remise, total}]
     * @param  array   $paiement   [{mode_paiement, montant_recu, remise, notes, date_vente}]
     * @param  int|null $clientId
     */
    public function creerVente(array $panier, array $paiement, ?int $clientId = null): Vente
    {
        return DB::transaction(function () use ($panier, $paiement, $clientId) {

            $parametres = Parametre::first();

            // ── Calcul totaux ──────────────────────────
            $sousTotal      = round(array_sum(array_column($panier, 'total')), 2);
            $remise         = (float) ($paiement['remise'] ?? 0);
            $total          = round($sousTotal - $remise, 2);
            $montantRecu    = (float) ($paiement['montant_recu'] ?? $total);
            $monnaieRendue  = round(max(0, $montantRecu - $total), 2);

            // ── Date de vente ──────────────────────────
            $dateVente = $paiement['date_vente'] ?? null;
            $venduLe   = ($dateVente && $dateVente !== now()->toDateString())
                ? Carbon::parse($dateVente)->startOfDay()
                : now();

            // ── Créer la vente ─────────────────────────
            $vente = Vente::create([
                'reference'      => $this->genererReference(),
                'client_id'      => $clientId,
                'user_id' => Auth::id(),
                'source'         => 'pos',
                'statut'         => 'completee',
                'total'          => $total,
                'remise'         => $remise,
                'montant_recu'   => $montantRecu,
                'monnaie_rendue' => $monnaieRendue,
                'mode_paiement'  => $paiement['mode_paiement'] ?? 'cash',
                'notes'          => $paiement['notes'] ?? null,
                'vendu_le'       => $venduLe,
            ]);

            // ── Lignes + stock ─────────────────────────
            foreach ($panier as $item) {
                VenteLigne::create([
                    'vente_id'      => $vente->id,
                    'plat_id'       => $item['plat_id'],
                    'nom_plat'      => $item['nom'],
                    'prix_unitaire' => $item['prix_unitaire'],
                    'quantite'      => $item['quantite'],
                    'remise'        => $item['remise'] ?? 0,
                    'total'         => $item['total'],
                ]);

                $plat = Plat::find($item['plat_id']);
                if ($plat) {
                    $this->stockService->decrementerStock(
                        $plat,
                        $item['quantite'],
                        'vente',
                        $vente->id,
                        'Vente #' . $vente->reference
                    );
                }
            }

            // ── Générer la facture ─────────────────────
            Facture::create([
                'vente_id'       => $vente->id,
                'client_id'      => $clientId,
                'numero_facture' => $this->genererNumeroFacture($parametres),
                'statut'         => 'emise',
                'total'          => $total,
                'emise_le'       => $venduLe,
            ]);

            // Incrémenter le compteur de factures
            $parametres?->increment('compteur_facture');

            return $vente->load(['lignes', 'facture', 'client']);
        });
    }

    private function genererReference(): string
    {
        $derniere = Vente::withTrashed()->latest()->first();
        $num      = $derniere
            ? (intval(substr($derniere->reference, -4)) + 1)
            : 1;
        return 'VNT-' . date('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    private function genererNumeroFacture(?Parametre $parametres): string
    {
        $prefixe  = $parametres?->prefixe_facture ?? 'FAC';
        $compteur = $parametres?->compteur_facture ?? 1;
        return $prefixe . '-' . date('Y') . '-' . str_pad($compteur, 4, '0', STR_PAD_LEFT);
    }
}

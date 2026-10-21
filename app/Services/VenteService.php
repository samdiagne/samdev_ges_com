<?php

namespace App\Services;

use App\Models\Vente;
use App\Models\VenteLigne;
use App\Models\Facture;
use App\Models\Parametre;
use App\Models\Produit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VenteService
{
    public function __construct(private StockService $stockService) {}

    /**
     * Créer une vente (POS) avec ses lignes, sa facture et les mouvements de stock.
     *
     * @param  array   $panier     Tableau de lignes [{produit_id, nom, prix_unitaire, quantite, remise, total}]
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

            // ── Répartition de la monnaie (Espèces vs Wave) ────
            $monnaieWave    = 0.0;
            $monnaieEspeces = 0.0;
            if ($monnaieRendue > 0) {
                $monnaieWave = round(min($monnaieRendue, max(0, (float) ($paiement['monnaie_rendue_wave'] ?? 0))), 2);
                $monnaieEspeces = round($monnaieRendue - $monnaieWave, 2);
            }

            // ── Date de vente ──────────────────────────
            $dateVente = $paiement['date_vente'] ?? null;
            $venduLe   = ($dateVente && $dateVente !== now()->toDateString())
                ? Carbon::parse($dateVente)->startOfDay()
                : now();

            $modePaiement = $paiement['mode_paiement'] ?? 'cash';
            $estARegler   = ($modePaiement === 'a_regler');

            $sessionCaisseId = $paiement['session_caisse_id'] ?? null;
            if (! $sessionCaisseId && Auth::check()) {
                $sessionCaisseId = \App\Models\SessionCaisse::where('user_id', Auth::id())
                    ->where('statut', 'ouverte')
                    ->latest('id')
                    ->value('id');
            }

            // ── Créer la vente ─────────────────────────
            $vente = Vente::create([
                'reference'              => $this->genererReference(),
                'client_id'              => $clientId,
                'user_id'                => Auth::id(),
                'session_caisse_id'      => $sessionCaisseId,
                'source'                 => 'boutique',
                'statut'                 => $estARegler ? 'en_attente' : 'completee',
                'total'                  => $total,
                'remise'                 => $remise,
                'montant_recu'           => $estARegler ? 0 : $montantRecu,
                'monnaie_rendue'         => $estARegler ? 0 : $monnaieRendue,
                'monnaie_rendue_especes' => $estARegler ? 0 : $monnaieEspeces,
                'monnaie_rendue_wave'    => $estARegler ? 0 : $monnaieWave,
                'mode_paiement'          => $modePaiement,
                'notes'                  => $paiement['notes'] ?? null,
                'vendu_le'               => $venduLe,
            ]);

            // ── Lignes + stock ─────────────────────────
            foreach ($panier as $item) {
                VenteLigne::create([
                    'vente_id'      => $vente->id,
                    'produit_id'    => $item['produit_id'],
                    'nom_produit'   => $item['nom'],
                    'details'       => $item['details'] ?? null,
                    'prix_unitaire' => $item['prix_unitaire'],
                    'quantite'      => $item['quantite'],
                    'remise'        => $item['remise'] ?? 0,
                    'total'         => $item['total'],
                ]);

                // Décrémenter le stock du produit principal (si non sur_commande)
                $plat = Produit::find($item['produit_id']);
                if ($plat && ! $plat->sur_commande && $plat->stock > 0) {
                    $this->stockService->decrementerStock(
                        $plat,
                        $item['quantite'],
                        'vente',
                        $vente->id,
                        'Vente #' . $vente->reference
                    );
                }

                // Décrémenter le stock du jus sélectionné dans le menu
                if (! empty($item['jus_id'])) {
                    $jus = Produit::find($item['jus_id']);
                    if ($jus && ! $jus->sur_commande && $jus->stock > 0) {
                        $this->stockService->decrementerStock(
                            $jus,
                            $item['quantite'],
                            'vente',
                            $vente->id,
                            'Vente #' . $vente->reference . ' (Menu: ' . $item['nom'] . ')'
                        );
                    }
                }

                // Décrémenter le stock du burger/tacos sélectionné dans le menu si géré en stock
                if (! empty($item['sandwich_id'])) {
                    $sandwich = Produit::find($item['sandwich_id']);
                    if ($sandwich && ! $sandwich->sur_commande && $sandwich->stock > 0) {
                        $this->stockService->decrementerStock(
                            $sandwich,
                            $item['quantite'],
                            'vente',
                            $vente->id,
                            'Vente #' . $vente->reference . ' (Menu: ' . $item['nom'] . ')'
                        );
                    }
                }
            }

            // ── Générer la facture ─────────────────────
            Facture::create([
                'vente_id'       => $vente->id,
                'client_id'      => $clientId,
                'numero_facture' => $this->genererNumeroFacture($parametres),
                'statut'         => $estARegler ? Facture::STATUT_A_REGLER : Facture::STATUT_PAYEE,
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
        $derniere = Vente::withTrashed()->latest('id')->first();
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

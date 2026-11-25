<?php

namespace App\Services;

use App\Models\Achat;
use App\Models\AchatReglement;
use App\Models\SessionCaisse;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AchatService
{
    /**
     * Retourne le solde total restant dû pour un fournisseur donné
     * (en excluant les achats annulés et éventuellement un achat précis).
     */
    public function soldeDuFournisseur(int $fournisseurId, ?int $excludeAchatId = null): float
    {
        return (float) Achat::where('fournisseur_id', $fournisseurId)
            ->where('statut', '!=', 'annule')
            ->where('montant_du', '>', 0)
            ->when($excludeAchatId, fn($q) => $q->where('id', '!=', $excludeAchatId))
            ->sum('montant_du');
    }

    /**
     * Retourne les achats impayés d'un fournisseur triés par ordre chronologique (FIFO).
     */
    public function achatsImpayesFournisseur(int $fournisseurId, ?int $prioriteAchatId = null): Collection
    {
        $achats = Achat::where('fournisseur_id', $fournisseurId)
            ->where('statut', '!=', 'annule')
            ->where('montant_du', '>', 0)
            ->orderBy('date_achat', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        if ($prioriteAchatId) {
            $prioritaire = $achats->firstWhere('id', $prioriteAchatId);
            if ($prioritaire) {
                $achats = collect([$prioritaire])
                    ->merge($achats->reject(fn($a) => $a->id === $prioriteAchatId))
                    ->values();
            }
        }

        return $achats;
    }

    /**
     * Enregistre un versement global pour un fournisseur et l'impute automatiquement
     * sur ses achats impayés (en priorité sur $prioriteAchatId si précisé, puis FIFO).
     *
     * @return array{total_impute: float, surplus: float, details: array}
     */
    public function reglerFournisseurFIFO(
        int $fournisseurId,
        float $montantVerse,
        string $modePaiement,
        ?SessionCaisse $session = null,
        ?string $notes = null,
        ?int $prioriteAchatId = null,
        ?Carbon $dateReglement = null
    ): array {
        return DB::transaction(function () use (
            $fournisseurId,
            $montantVerse,
            $modePaiement,
            $session,
            $notes,
            $prioriteAchatId,
            $dateReglement
        ) {
            $resteAImputer = round(max(0, $montantVerse), 2);
            $totalImpute   = 0.0;
            $details       = [];
            $date          = $dateReglement ?? now();

            $achatsImpayes = $this->achatsImpayesFournisseur($fournisseurId, $prioriteAchatId);

            foreach ($achatsImpayes as $achat) {
                if ($resteAImputer <= 0) {
                    break;
                }

                $du = (float) $achat->montant_du;
                if ($du <= 0) {
                    continue;
                }

                $montantPourCetAchat = round(min($resteAImputer, $du), 2);

                AchatReglement::create([
                    'reference'         => AchatReglement::genererReference(),
                    'achat_id'          => $achat->id,
                    'session_caisse_id' => $session?->id,
                    'user_id'           => Auth::id(),
                    'montant'           => $montantPourCetAchat,
                    'mode_paiement'     => $modePaiement ?: 'cash',
                    'date_reglement'    => $date,
                    'notes'             => $notes ?: ($session
                        ? 'Règlement compte fournisseur sur session ' . $session->reference
                        : 'Règlement compte fournisseur'),
                ]);

                $achat->recalculerPaiement();

                if ((float) $achat->montant_du <= 0 && $achat->statut !== 'recu') {
                    $achat->update(['statut' => 'recu']);
                }

                $resteAImputer = round($resteAImputer - $montantPourCetAchat, 2);
                $totalImpute   = round($totalImpute + $montantPourCetAchat, 2);

                $details[] = [
                    'achat_id'  => $achat->id,
                    'reference' => $achat->reference,
                    'montant'   => $montantPourCetAchat,
                    'reste_du'  => (float) $achat->montant_du,
                ];
            }

            return [
                'total_impute' => $totalImpute,
                'surplus'      => $resteAImputer,
                'details'      => $details,
            ];
        });
    }
}

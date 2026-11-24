<?php

namespace App\Livewire\Rapports;

use Livewire\Component;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Models\Achat;
use App\Models\AchatReglement;
use App\Models\Depense;
use App\Models\Produit;
use App\Models\Client;
use Carbon\Carbon;

class RapportManager extends Component
{
    public string $dateFrom    = '';
    public string $dateTo      = '';
    public string $typeRapport = 'ventes';

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo   = now()->format('Y-m-d');
    }

    // ─── Raccourcis période ────────────────────────────────────────────────────

    public function periodeAujourdhui(): void
    {
        $this->dateFrom = now()->toDateString();
        $this->dateTo   = now()->toDateString();
    }

    public function periode7Jours(): void
    {
        $this->dateFrom = now()->subDays(6)->toDateString();
        $this->dateTo   = now()->toDateString();
    }

    public function periodeCeMois(): void
    {
        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo   = now()->toDateString();
    }

    public function periodeMoisDernier(): void
    {
        $this->dateFrom = now()->subMonth()->startOfMonth()->toDateString();
        $this->dateTo   = now()->subMonth()->endOfMonth()->toDateString();
    }

    /**
     * Applique le filtre de date — appelé par le bouton "Appliquer"
     * ou déclenché automatiquement via wire:change.
     */
    public function appliquerFiltre(): void
    {
        // Validation légère : s'assurer que les dates sont cohérentes
        if ($this->dateFrom && $this->dateTo && $this->dateFrom > $this->dateTo) {
            $this->dateTo = $this->dateFrom;
        }
        // Le re-rendu se fait automatiquement
    }

    public function render()
    {
        // Protection contre dates vides ou invalides
        $dateFrom = $this->dateFrom ?: now()->startOfMonth()->format('Y-m-d');
        $dateTo   = $this->dateTo   ?: now()->format('Y-m-d');

        $from = Carbon::parse($dateFrom)->startOfDay();
        $to   = Carbon::parse($dateTo)->endOfDay();

        // Cohérence : from ne peut pas être après to
        if ($from->gt($to)) {
            $to = $from->copy()->endOfDay();
        }

        $rapport = match ($this->typeRapport) {
            'ventes'     => $this->rapportVentes($from, $to),
            'produits'   => $this->rapportProduits($from, $to),
            'clients'    => $this->rapportClients($from, $to),
            'achats'     => $this->rapportAchats($from, $to),
            'tresorerie' => $this->rapportTresorerie($from, $to),
            default      => [],
        };

        return view('livewire.rapports.rapport-manager', compact('rapport'));
    }

    private function rapportVentes(Carbon $from, Carbon $to): array
    {
        $ventes = Vente::with(['client', 'lignes'])
            ->where('statut', 'completee')
            ->whereBetween('vendu_le', [$from, $to])
            ->orderBy('vendu_le')
            ->get();

        // Ventiler les paiements multiples par moyen de paiement réel
        $parMode = [];
        $canauxMultiple = [
            'cash'  => 'montant_cash',
            'wave'  => 'montant_wave',
            'om'    => 'montant_om',
            'carte' => 'montant_carte',
        ];

        foreach ($ventes as $vente) {
            if ($vente->mode_paiement === 'multiple') {
                foreach ($canauxMultiple as $modeKey => $colonne) {
                    $montant = (float) ($vente->{$colonne} ?? 0);
                    if ($montant > 0) {
                        if (!isset($parMode[$modeKey])) {
                            $parMode[$modeKey] = ['total' => 0.0, 'count' => 0];
                        }
                        $parMode[$modeKey]['total'] += $montant;
                        $parMode[$modeKey]['count'] += 1;
                    }
                }
            } else {
                $modeKey = $vente->mode_paiement === 'card' ? 'carte' : $vente->mode_paiement;
                if (!isset($parMode[$modeKey])) {
                    $parMode[$modeKey] = ['total' => 0.0, 'count' => 0];
                }
                $parMode[$modeKey]['total'] += (float) $vente->total;
                $parMode[$modeKey]['count'] += 1;
            }
        }

        return [
            'total'         => $ventes->sum('total'),
            'count'         => $ventes->count(),
            'panier_moyen'  => $ventes->count() > 0 ? round($ventes->sum('total') / $ventes->count()) : 0,
            'par_mode'      => $parMode,
            'par_source'    => $ventes->groupBy('source')
                ->map(fn($g) => ['total' => $g->sum('total'), 'count' => $g->count()]),
            'par_jour'      => $ventes->groupBy(fn($v) => $v->vendu_le?->format('d/m/Y') ?? 'Inconnue')
                ->map(fn($g) => $g->sum('total')),
        ];
    }

    private function rapportProduits(Carbon $from, Carbon $to): array
    {
        $lignes = VenteLigne::with('produit.categorie')
            ->whereHas('vente', fn($q) =>
                $q->where('statut', 'completee')->whereBetween('vendu_le', [$from, $to])
            )
            ->selectRaw('produit_id, nom_produit, SUM(quantite) as total_qte, SUM(total) as total_ca')
            ->groupBy('produit_id', 'nom_produit')
            ->orderByDesc('total_ca')
            ->get();

        $caVentes = (float) Vente::where('statut', 'completee')
            ->whereBetween('vendu_le', [$from, $to])
            ->sum('total');
        $remisesGlobales = (float) Vente::where('statut', 'completee')
            ->whereBetween('vendu_le', [$from, $to])
            ->sum('remise');

        return [
            'lignes'           => $lignes,
            'total_ca'         => $lignes->sum('total_ca'),
            'ca_ventes'        => $caVentes,
            'remises_globales' => $remisesGlobales,
            'total_portions'   => $lignes->sum('total_qte'),
            'par_categorie'    => $lignes->groupBy(fn($l) => $l->produit?->categorie?->nom ?? 'Non catégorisé')
                ->map(fn($g) => ['ca' => $g->sum('total_ca'), 'qte' => $g->sum('total_qte')]),
        ];
    }

    private function rapportClients(Carbon $from, Carbon $to): array
    {
        $ventes = Vente::with('client')
            ->where('statut', 'completee')
            ->whereBetween('vendu_le', [$from, $to])
            ->get();

        $parClient = $ventes->groupBy('client_id')->map(fn($g) => [
            'nom'    => $g->first()?->client?->nom ?? 'Client anonyme',
            'total'  => $g->sum('total'),
            'count'  => $g->count(),
        ])->sortByDesc('total');

        return [
            'par_client'     => $parClient,
            'total_clients'  => $parClient->count(),
            'client_top'     => $parClient->first(),
            'nouveaux'       => Client::whereBetween('created_at', [$from, $to])->count(),
        ];
    }

    private function rapportAchats(Carbon $from, Carbon $to): array
    {
        $achats = Achat::with(['fournisseur', 'lignes'])
            ->whereBetween('date_achat', [$from->toDateString(), $to->toDateString()])
            ->get();

        return [
            'total'           => $achats->sum('montant_total'),
            'count'           => $achats->count(),
            'paye'            => $achats->sum('montant_paye'),
            'du'              => $achats->sum('montant_du'),
            'par_fournisseur' => $achats->groupBy('fournisseur_id')
                ->map(fn($g) => [
                    'nom'   => $g->first()?->fournisseur?->nom ?? 'Sans fournisseur',
                    'total' => $g->sum('montant_total'),
                    'count' => $g->count(),
                ])->sortByDesc('total'),
        ];
    }

    private function rapportTresorerie(Carbon $from, Carbon $to): array
    {
        $recettes = Vente::where('statut', 'completee')
            ->whereBetween('vendu_le', [$from, $to])->sum('total');

        $achats = AchatReglement::whereBetween('date_reglement', [$from, $to])
            ->sum('montant');

        $depenses = Depense::whereBetween('date_depense',
            [$from->toDateString(), $to->toDateString()])->sum('montant');

        return [
            'recettes' => $recettes,
            'achats'   => $achats,
            'depenses' => $depenses,
            'solde'    => $recettes - $achats - $depenses,
        ];
    }
}

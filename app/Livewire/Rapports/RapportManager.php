<?php

namespace App\Livewire\Rapports;

use Livewire\Component;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Models\Achat;
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

    public function render()
    {
        $from = Carbon::parse($this->dateFrom)->startOfDay();
        $to   = Carbon::parse($this->dateTo)->endOfDay();

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
            ->get();

        return [
            'total'         => $ventes->sum('total'),
            'count'         => $ventes->count(),
            'panier_moyen'  => $ventes->count() > 0 ? round($ventes->sum('total') / $ventes->count()) : 0,
            'par_mode'      => $ventes->groupBy('mode_paiement')
                ->map(fn($g) => ['total' => $g->sum('total'), 'count' => $g->count()]),
            'par_source'    => $ventes->groupBy('source')
                ->map(fn($g) => ['total' => $g->sum('total'), 'count' => $g->count()]),
            'par_jour'      => $ventes->groupBy(fn($v) => $v->vendu_le->format('d/m/Y'))
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

        return [
            'lignes'         => $lignes,
            'total_ca'       => $lignes->sum('total_ca'),
            'total_portions' => $lignes->sum('total_qte'),
            'par_categorie'  => $lignes->groupBy(fn($l) => $l->produit?->categorie?->nom ?? 'Non catégorisé')
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

        $achats = Achat::whereBetween('date_achat',
            [$from->toDateString(), $to->toDateString()])->sum('montant_paye');

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

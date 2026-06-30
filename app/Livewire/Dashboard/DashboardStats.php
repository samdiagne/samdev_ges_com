<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Models\Achat;
use App\Models\Depense;
use App\Models\Produit;
use App\Models\Client;
use App\Models\Commande;
use Carbon\Carbon;

class DashboardStats extends Component
{
    public string $periode = 'mois';

    public function render()
    {
        $stats  = $this->getStats();
        $charts = $this->getChartData();
        $alertes = $this->getAlertes();
        $recent = $this->getActiviteRecente();

        $this->dispatch('chartsUpdated', charts: $charts);

        return view('livewire.dashboard.dashboard-stats',
            compact('stats', 'charts', 'alertes', 'recent'));
    }

    private function getStats(): array
    {
        [$from, $to] = $this->getDateRange();

        $ventesQuery = Vente::where('statut', 'completee')
            ->whereBetween('vendu_le', [$from, $to]);

        $ca          = $ventesQuery->sum('total');
        $nbVentes    = $ventesQuery->count();

        $achatsTotal   = Achat::whereBetween('date_achat', [$from->toDateString(), $to->toDateString()])->sum('montant_total');
        $depensesTotal = Depense::whereBetween('date_depense', [$from->toDateString(), $to->toDateString()])->sum('montant');
        $benefice      = $ca - $achatsTotal - $depensesTotal;

        [$prevFrom, $prevTo] = $this->getPreviousDateRange();
        $prevCa = Vente::where('statut', 'completee')->whereBetween('vendu_le', [$prevFrom, $prevTo])->sum('total');
        $evolution = $prevCa > 0 ? round((($ca - $prevCa) / $prevCa) * 100, 1) : 0;

        return [
            'ca'             => $ca,
            'nb_ventes'      => $nbVentes,
            'achats_total'   => $achatsTotal,
            'depenses_total' => $depensesTotal,
            'benefice'       => $benefice,
            'evolution_ca'   => $evolution,
            'panier_moyen'   => $nbVentes > 0 ? round($ca / $nbVentes) : 0,
            'total_produits'  => Produit::where('est_actif', true)->count(),
            'total_clients'   => Client::count(),
            'stock_faible'    => Produit::whereColumn('stock', '<=', 'seuil_alerte_stock')->where('stock', '>', 0)->where('sur_commande', false)->count(),
            'stock_rupture'   => Produit::where('stock', '<=', 0)->where('sur_commande', false)->count(),
            'cash_total'     => (clone $ventesQuery)->where('mode_paiement', 'cash')->sum('total'),
            'wave_total'     => (clone $ventesQuery)->where('mode_paiement', 'wave')->sum('total'),
            'om_total'       => (clone $ventesQuery)->where('mode_paiement', 'om')->sum('total'),
            'carte_total'    => (clone $ventesQuery)->where('mode_paiement', 'carte')->sum('total'),
        ];
    }

    private function getChartData(): array
    {
        $jours = [];
        $ventes = [];
        $comptes = [];

        for ($i = 6; $i >= 0; $i--) {
            $date      = Carbon::today()->subDays($i);
            $jours[]   = $date->translatedFormat('D d/m');
            $ventes[]  = (float) Vente::where('statut', 'completee')->whereDate('vendu_le', $date)->sum('total');
            $comptes[]  = Vente::where('statut', 'completee')->whereDate('vendu_le', $date)->count();
        }

        $mois   = [];
        $caParMois = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = Carbon::today()->startOfMonth()->subMonths($i);
            $mois[]      = $m->translatedFormat('M Y');
            $caParMois[] = (float) Vente::where('statut', 'completee')
                ->whereYear('vendu_le', $m->year)->whereMonth('vendu_le', $m->month)->sum('total');
        }

        [$from, $to] = $this->getDateRange();
        $paiements = Vente::where('statut', 'completee')
            ->whereBetween('vendu_le', [$from, $to])
            ->selectRaw('mode_paiement, SUM(total) as total')
            ->groupBy('mode_paiement')
            ->get()->pluck('total', 'mode_paiement')->toArray();

        return [
            'labels_jours'   => $jours,
            'ventes_jours'   => $ventes,
            'comptes_jours'  => $comptes,
            'labels_mois'    => $mois,
            'ca_par_mois'    => $caParMois,
            'paiements'      => $paiements,
        ];
    }

    private function getAlertes(): array
    {
        return [
            'stock_faible' => Produit::with('categorie')
                ->whereColumn('stock', '<=', 'seuil_alerte_stock')
                ->where('stock', '>', 0)
                ->where('sur_commande', false)
                ->orderBy('stock')->limit(5)->get(),
            'stock_rupture' => Produit::with('categorie')
                ->where('stock', '<=', 0)->where('sur_commande', false)->limit(5)->get(),
        ];
    }

    private function getActiviteRecente(): array
    {
        return [
            'ventes' => Vente::with(['client', 'user'])->latest('vendu_le')->limit(5)->get(),
            'commandes_en_attente' => Commande::with('lignes')
                ->whereIn('statut', ['en_attente', 'confirmee', 'en_preparation'])
                ->latest()->limit(6)->get(),
            'top_produits' => VenteLigne::selectRaw('produit_id, nom_produit, SUM(quantite) as total_qty, SUM(total) as total_ca')
                ->whereHas('vente', fn($q) =>
                    $q->where('statut', 'completee')->whereMonth('vendu_le', now()->month)
                )
                ->groupBy('produit_id', 'nom_produit')
                ->orderByDesc('total_qty')
                ->limit(5)->get(),
        ];
    }

    private function getDateRange(): array
    {
        return match($this->periode) {
            'aujourd_hui' => [Carbon::today(),              Carbon::today()->endOfDay()],
            'semaine'     => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'mois'        => [Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()],
            'annee'       => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default       => [Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()],
        };
    }

    private function getPreviousDateRange(): array
    {
        return match($this->periode) {
            'aujourd_hui' => [Carbon::yesterday(),                      Carbon::yesterday()->endOfDay()],
            'semaine'     => [Carbon::now()->subWeek()->startOfWeek(),  Carbon::now()->subWeek()->endOfWeek()],
            'mois'        => [Carbon::now()->subMonth()->startOfMonth(),Carbon::now()->subMonth()->endOfMonth()],
            'annee'       => [Carbon::now()->subYear()->startOfYear(),  Carbon::now()->subYear()->endOfYear()],
            default       => [Carbon::now()->subMonth()->startOfMonth(),Carbon::now()->subMonth()->endOfMonth()],
        };
    }
}

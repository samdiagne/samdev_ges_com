<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use App\Models\StockMovement;
use Carbon\Carbon;

class DashboardStats extends Component
{
    public string $period = 'today';

    public function render()
    {
        $stats   = $this->getStats();
        $charts  = $this->getChartData();
        $alerts  = $this->getAlerts();
        $recent  = $this->getRecentActivity();

        // Dispatcher les données des graphiques au frontend
        $this->dispatch('chartsUpdated', charts: $charts);

        return view('livewire.dashboard.dashboard-stats',
            compact('stats', 'charts', 'alerts', 'recent'));
    }

    private function getStats(): array
    {
        [$from, $to] = $this->getDateRange();

        // Ventes période
        $salesQuery = Sale::where('status', 'completed')
            ->whereBetween('sold_at', [$from, $to]);

        $ca         = $salesQuery->sum('total_ttc');
        $salesCount = $salesQuery->count();
        $caHt       = $salesQuery->sum('subtotal_ht');
        $tvaTotal   = $salesQuery->sum('tva_amount');

        // Achats période
        $purchasesQuery = Purchase::whereBetween('purchase_date', [
            $from->toDateString(), $to->toDateString()
        ]);
        $achatsTotal = $purchasesQuery->sum('total_amount');

        // Dépenses période
        $depensesQuery = Expense::whereBetween('expense_date', [
            $from->toDateString(), $to->toDateString()
        ]);
        $depensesTotal = $depensesQuery->sum('amount');

        // Bénéfice brut = CA - Achats - Dépenses
        $benefice = $caHt - $achatsTotal - $depensesTotal;

        // Comparaison période précédente
        [$prevFrom, $prevTo] = $this->getPreviousDateRange();
        $prevCa = Sale::where('status', 'completed')
            ->whereBetween('sold_at', [$prevFrom, $prevTo])
            ->sum('total_ttc');
        $caEvolution = $prevCa > 0
            ? round((($ca - $prevCa) / $prevCa) * 100, 1)
            : 0;

        return [
            'ca'            => $ca,
            'ca_ht'         => $caHt,
            'tva_total'     => $tvaTotal,
            'sales_count'   => $salesCount,
            'achats_total'  => $achatsTotal,
            'depenses_total'=> $depensesTotal,
            'benefice'      => $benefice,
            'ca_evolution'  => $caEvolution,
            'avg_basket'    => $salesCount > 0 ? round($ca / $salesCount) : 0,

            // Globaux
            'total_products'  => Product::where('is_active', true)->count(),
            'total_customers' => Customer::count(),
            'stock_low'       => Product::whereColumn('stock_quantity', '<=', 'stock_alert_threshold')
                                        ->where('stock_quantity', '>', 0)->count(),
            'stock_out'       => Product::where('stock_quantity', '<=', 0)->count(),

            // Paiements
            'cash_total' => (clone $salesQuery)->where('payment_method', 'cash')->sum('total_ttc'),
            'wave_total' => (clone $salesQuery)->where('payment_method', 'wave')->sum('total_ttc'),
            'om_total'   => (clone $salesQuery)->where('payment_method', 'om')->sum('total_ttc'),
            'card_total'   => (clone $salesQuery)->where('payment_method', 'card')->sum('total_ttc'),
        ];
    }

    private function getChartData(): array
    {
        // Ventes 7 derniers jours
        $days   = [];
        $sales  = [];
        $counts = [];

        for ($i = 6; $i >= 0; $i--) {
            $date    = Carbon::today()->subDays($i);
            $days[]  = $date->translatedFormat('D d/m');
            $daySales = Sale::where('status', 'completed')
                ->whereDate('sold_at', $date)
                ->sum('total_ttc');
            $sales[]  = (float) $daySales;
            $counts[] = Sale::where('status', 'completed')
                ->whereDate('sold_at', $date)
                ->count();
        }

        // CA par mois (12 derniers mois)
        $months    = [];
        $monthlyCA = [];

        for ($i = 11; $i >= 0; $i--) {
            $month      = Carbon::today()->startOfMonth()->subMonths($i);
            $months[]   = $month->translatedFormat('M Y');
            $monthlyCA[]= (float) Sale::where('status', 'completed')
                ->whereYear('sold_at', $month->year)
                ->whereMonth('sold_at', $month->month)
                ->sum('total_ttc');
        }

        // Répartition paiements (période courante)
        [$from, $to] = $this->getDateRange();
        $paymentData = Sale::where('status', 'completed')
            ->whereBetween('sold_at', [$from, $to])
            ->selectRaw('payment_method, SUM(total_ttc) as total')
            ->groupBy('payment_method')
            ->get()
            ->pluck('total', 'payment_method')
            ->toArray();

        return [
            'daily_labels'  => $days,
            'daily_sales'   => $sales,
            'daily_counts'  => $counts,
            'monthly_labels'=> $months,
            'monthly_ca'    => $monthlyCA,
            'payment_data'  => $paymentData,
        ];
    }

    private function getAlerts(): array
    {
        return [
            'low_stock' => Product::with('category')
                ->whereColumn('stock_quantity', '<=', 'stock_alert_threshold')
                ->where('stock_quantity', '>', 0)
                ->orderBy('stock_quantity')
                ->limit(5)
                ->get(),
            'out_stock' => Product::with('category')
                ->where('stock_quantity', '<=', 0)
                ->limit(5)
                ->get(),
        ];
    }

    private function getRecentActivity(): array
    {
        return [
            'sales' => Sale::with(['customer', 'user'])
                ->latest('sold_at')
                ->limit(5)
                ->get(),
            'pending_orders' => Order::with('items')
                ->whereIn('status', ['pending', 'confirmed'])
                ->latest()
                ->limit(6)
                ->get(),
            'top_products' => \App\Models\SaleItem::selectRaw(
                    'product_id, product_name,
                     SUM(quantity) as total_qty,
                     SUM(total_ttc) as total_ca'
                )
                ->whereHas('sale', fn($q) =>
                    $q->where('status', 'completed')
                      ->whereMonth('sold_at', now()->month)
                )
                ->groupBy('product_id', 'product_name')
                ->orderByDesc('total_qty')
                ->limit(5)
                ->get(),
        ];
    }

    private function getDateRange(): array
    {
        return match($this->period) {
            'today'   => [Carbon::today(),            Carbon::today()->endOfDay()],
            'week'    => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month'   => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'year'    => [Carbon::now()->startOfYear(),  Carbon::now()->endOfYear()],
            default   => [Carbon::today(),             Carbon::today()->endOfDay()],
        };
    }

    private function getPreviousDateRange(): array
    {
        return match($this->period) {
            'today'  => [Carbon::yesterday(),                    Carbon::yesterday()->endOfDay()],
            'week'   => [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()],
            'month'  => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'year'   => [Carbon::now()->subYear()->startOfYear(),   Carbon::now()->subYear()->endOfYear()],
            default  => [Carbon::yesterday(),                    Carbon::yesterday()->endOfDay()],
        };
    }
}
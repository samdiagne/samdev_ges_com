<?php

namespace App\Livewire\Rapports;

use Livewire\Component;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Carbon\Carbon;

class RapportManager extends Component
{
    public string $activeTab  = 'ventes';
    public string $dateFrom   = '';
    public string $dateTo     = '';
    public string $filterMonth= '';
    public string $filterYear = '';

    public function mount(): void
    {
        $this->filterMonth = now()->format('Y-m');
        $this->filterYear  = now()->format('Y');
        $this->dateFrom    = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo      = now()->format('Y-m-d');
    }

    public function render()
    {
        $data = match($this->activeTab) {
            'ventes'    => $this->getVentesData(),
            'stocks'    => $this->getStocksData(),
            'comptabilite' => $this->getComptaData(),
            default     => $this->getVentesData(),
        };

        return view('livewire.rapports.rapport-manager', array_merge(
            ['activeTab' => $this->activeTab],
            $data
        ));
    }

    // ── Rapport Ventes ────────────────────────────
    private function getVentesData(): array
    {
        $from = Carbon::parse($this->dateFrom)->startOfDay();
        $to   = Carbon::parse($this->dateTo)->endOfDay();

        $sales = Sale::with(['customer', 'items'])
            ->where('status', 'completed')
            ->whereBetween('sold_at', [$from, $to])
            ->latest('sold_at')
            ->get();

        // Stats globales
        $totalCa    = $sales->sum('total_ttc');
        $totalHt    = $sales->sum('subtotal_ht');
        $totalRemise= $sales->sum('discount');
        $nbVentes   = $sales->count();
        $panierMoyen= $nbVentes > 0 ? round($totalCa / $nbVentes) : 0;

        // Par mode de paiement
        $byPayment = $sales->groupBy('payment_method')
            ->map(fn($g) => [
                'count'  => $g->count(),
                'total'  => $g->sum('total_ttc'),
            ]);

        // Par jour
        $byDay = $sales->groupBy(fn($s) => $s->sold_at->format('d/m/Y'))
            ->map(fn($g) => [
                'count' => $g->count(),
                'total' => $g->sum('total_ttc'),
            ]);

        // Top produits
        $topProducts = SaleItem::whereHas('sale', fn($q) =>
            $q->where('status', 'completed')
              ->whereBetween('sold_at', [$from, $to])
        )
        ->selectRaw('product_name, SUM(quantity) as qty, SUM(total_ttc) as ca')
        ->groupBy('product_name')
        ->orderByDesc('ca')
        ->limit(10)
        ->get();

        return compact(
            'sales', 'totalCa',
            'totalRemise', 'nbVentes', 'panierMoyen',
            'byPayment', 'byDay', 'topProducts'
        );
    }

    // ── Rapport Stocks ────────────────────────────
    private function getStocksData(): array
    {
        $products = Product::with('category')
            ->orderBy('stock_quantity')
            ->get();

        $movements = StockMovement::with(['product', 'user'])
            ->whereBetween('moved_at', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay(),
            ])
            ->latest('moved_at')
            ->limit(50)
            ->get();

        $stockStats = [
            'total_products' => $products->count(),
            'total_value'    => $products->sum(fn($p) =>
                $p->stock_quantity * $p->purchase_price
            ),
            'out_of_stock'   => $products->where('stock_quantity', '<=', 0)->count(),
            'low_stock'      => $products->filter(fn($p) =>
                $p->stock_quantity > 0 &&
                $p->stock_quantity <= $p->stock_alert_threshold
            )->count(),
        ];

        return compact('products', 'movements', 'stockStats');
    }

    // ── Rapport Comptabilité ──────────────────────
    private function getComptaData(): array
    {
        $year = (int) $this->filterYear;

        // Données mensuelles
        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $from = Carbon::create($year, $m, 1)->startOfMonth();
            $to   = Carbon::create($year, $m, 1)->endOfMonth();

            $ca       = Sale::where('status', 'completed')
                ->whereYear('sold_at', $year)->whereMonth('sold_at', $m)
                ->sum('total_ttc');
            $caHt     = Sale::where('status', 'completed')
                ->whereYear('sold_at', $year)->whereMonth('sold_at', $m)
                ->sum('subtotal_ht');
            $achats   = Purchase::whereYear('purchase_date', $year)
                ->whereMonth('purchase_date', $m)->sum('total_amount');
            $depenses = Expense::whereYear('expense_date', $year)
                ->whereMonth('expense_date', $m)->sum('amount');
            $benefice = $caHt - $achats - $depenses;

            $monthly[] = [
                'mois'     => Carbon::create($year, $m, 1)->translatedFormat('F'),
                'ca'       => $ca,
                'achats'   => $achats,
                'depenses' => $depenses,
                'benefice' => $benefice,
            ];
        }

        // Totaux annuels
        $annuel = [
            'ca'       => array_sum(array_column($monthly, 'ca')),
            'achats'   => array_sum(array_column($monthly, 'achats')),
            'depenses' => array_sum(array_column($monthly, 'depenses')),
            'benefice' => array_sum(array_column($monthly, 'benefice')),
        ];

        return compact('monthly', 'annuel', 'year');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\CompanySetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EtatController extends Controller
{
    // ── État des ventes ───────────────────────────
    public function etatVentes(Request $request)
    {
        $from = Carbon::parse($request->date_from ?? now()->startOfMonth());
        $to   = Carbon::parse($request->date_to ?? now())->endOfDay();

        $sales = Sale::with(['customer', 'items'])
            ->where('status', 'completed')
            ->whereBetween('sold_at', [$from, $to])
            ->latest('sold_at')
            ->get();

        $stats = [
            'total'       => $sales->sum('total_ttc'),
            'count'       => $sales->count(),
            'cash'        => $sales->where('payment_method', 'cash')->sum('total_ttc'),
            'wave'        => $sales->where('payment_method', 'wave')->sum('total_ttc'),
            'om'          => $sales->where('payment_method', 'om')->sum('total_ttc'),
            'card'        => $sales->where('payment_method', 'card')->sum('total_ttc'),
            'avg_basket'  => $sales->count() > 0
                ? round($sales->sum('total_ttc') / $sales->count()) : 0,
        ];

        $settings      = CompanySetting::getSettings();
        $logoBase64 = $this->getLogoBase64();


        return Pdf::loadView('pdf.etats.etat-ventes',
            compact('sales', 'stats', 'settings', 'logoBase64', 'from', 'to')
        )
        ->setPaper('a4', 'portrait')
        ->setOptions(['defaultFont' => 'DejaVu Sans', 'isHtml5ParserEnabled' => true])
        ->download('etat-ventes-' . $from->format('Y-m-d') . '.pdf');
    }

    // ── État des dépenses ─────────────────────────
    public function etatDepenses(Request $request)
    {
        $from = Carbon::parse($request->date_from ?? now()->startOfMonth());
        $to   = Carbon::parse($request->date_to ?? now())->endOfDay();

        $expenses = Expense::with(['category', 'user'])
            ->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
            ->latest('expense_date')
            ->get();

        $stats = [
            'total'        => $expenses->sum('amount'),
            'count'        => $expenses->count(),
            'by_category'  => $expenses->groupBy('category.name')
                ->map(fn($g) => $g->sum('amount')),
        ];

        $settings = CompanySetting::getSettings();
        $logoBase64 = $this->getLogoBase64();


        return Pdf::loadView('pdf.etats.etat-depenses',
            compact('expenses', 'stats', 'settings', 'logoBase64', 'from', 'to')
        )
        ->setPaper('a4', 'portrait')
        ->setOptions(['defaultFont' => 'DejaVu Sans', 'isHtml5ParserEnabled' => true])
        ->download('etat-depenses-' . $from->format('Y-m-d') . '.pdf');
    }

    // ── État de trésorerie ────────────────────────
    public function etatTresorerie(Request $request)
    {
        $from = Carbon::parse($request->date_from ?? now()->startOfMonth());
        $to   = Carbon::parse($request->date_to ?? now())->endOfDay();

        $recettes = Sale::where('status', 'completed')
            ->whereBetween('sold_at', [$from, $to])
            ->sum('total_ttc');

        $achats   = Purchase::whereBetween('purchase_date', [
            $from->toDateString(), $to->toDateString()
        ])->sum('amount_paid');

        $depenses = Expense::whereBetween('expense_date', [
            $from->toDateString(), $to->toDateString()
        ])->sum('amount');

        $solde = $recettes - $achats - $depenses;

        // Mouvements journaliers
        $mouvements = [];
        $current = $from->copy();
        while ($current->lte($to)) {
            $dateStr  = $current->toDateString();
            $recette  = Sale::where('status', 'completed')
                ->whereDate('sold_at', $dateStr)->sum('total_ttc');
            $achat    = Purchase::whereDate('purchase_date', $dateStr)->sum('amount_paid');
            $depense  = Expense::whereDate('expense_date', $dateStr)->sum('amount');

            if ($recette > 0 || $achat > 0 || $depense > 0) {
                $mouvements[] = [
                    'date'    => $current->translatedFormat('d/m/Y'),
                    'recette' => $recette,
                    'achat'   => $achat,
                    'depense' => $depense,
                    'solde'   => $recette - $achat - $depense,
                ];
            }
            $current->addDay();
        }

        $stats    = compact('recettes', 'achats', 'depenses', 'solde');
        $settings = CompanySetting::getSettings();
        $logoBase64 = $this->getLogoBase64();

        return Pdf::loadView('pdf.etats.etat-tresorerie',
            compact('mouvements', 'stats', 'settings', 'logoBase64', 'from', 'to')
        )
        ->setPaper('a4', 'portrait')
        ->setOptions(['defaultFont' => 'DejaVu Sans', 'isHtml5ParserEnabled' => true])
        ->download('etat-tresorerie-' . $from->format('Y-m-d') . '.pdf');
    }

    // ── Bilan stocks ──────────────────────────────
    public function bilanStocks(Request $request)
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        $stats = [
            'total_produits'     => $products->count(),
            'valeur_achat'       => $products->sum(fn($p) =>
                $p->stock_quantity * $p->purchase_price),
            'valeur_vente'       => $products->sum(fn($p) =>
                $p->stock_quantity * $p->selling_price),
            'ruptures'           => $products->where('stock_quantity', '<=', 0)->count(),
            'stock_bas'          => $products->filter(fn($p) =>
                $p->stock_quantity > 0 &&
                $p->stock_quantity <= $p->stock_alert_threshold
            )->count(),
        ];

        $stats['marge_potentielle'] = $stats['valeur_vente'] - $stats['valeur_achat'];

        $settings = CompanySetting::getSettings();

        $logoBase64 = $this->getLogoBase64();

        return Pdf::loadView('pdf.etats.bilan-stocks',
            compact('products', 'stats', 'settings', 'logoBase64')
        )
        ->setPaper('a4', 'landscape')
        ->setOptions(['defaultFont' => 'DejaVu Sans', 'isHtml5ParserEnabled' => true])
        ->download('bilan-stocks-' . now()->format('Y-m-d') . '.pdf');
    }

    private function getLogoBase64(): ?string
    {
        $settings = CompanySetting::getSettings();
        if (!$settings->logo) return null;
        
        $path = Storage::disk('public')->path($settings->logo);
        if (!file_exists($path)) return null;
        
        $type    = pathinfo($path, PATHINFO_EXTENSION);
        $data    = base64_encode(file_get_contents($path));
        return "data:image/{$type};base64,{$data}";
    }
}
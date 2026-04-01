<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class FactureController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    // Vue détail facture
    public function show(Sale $sale)
    {
        $sale->load(['items.product', 'customer', 'invoice', 'user']);

        // Toujours regénérer pour avoir les dernières infos
        if ($sale->invoice) {
            $this->invoiceService->generatePdf($sale);
        }

        return view('factures.show', compact('sale'));
    }

    // Télécharger PDF
    public function download(Sale $sale)
    {
        $sale->load(['items', 'customer', 'invoice']);
        return $this->invoiceService->download($sale);
    }
}
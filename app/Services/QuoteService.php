<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\CompanySetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuoteService
{
    public function __construct(private StockService $stockService) {}

    // ── Générer PDF devis ─────────────────────────
    public function generatePdf(Quote $quote): string
    {
        $quote->load(['items', 'customer']);
        $settings      = CompanySetting::getSettings();
        $logoPath      = $settings->logo
            ? Storage::disk('public')->path($settings->logo) : null;
        $stampPath     = $settings->stamp_image
            ? Storage::disk('public')->path($settings->stamp_image) : null;
        $signaturePath = $settings->signature_image
            ? Storage::disk('public')->path($settings->signature_image) : null;

        $pdf = Pdf::loadView('pdf.devis', compact(
            'quote', 'settings', 'logoPath', 'stampPath', 'signaturePath'
        ))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isRemoteEnabled'      => false,
            'isHtml5ParserEnabled' => true,
            'chroot'               => storage_path('app/public'),
        ]);

        $filename = 'devis/' . $quote->reference . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }

    public function download(Quote $quote)
    {
        $quote->load(['items', 'customer']);
        $settings      = CompanySetting::getSettings();
        $logoPath      = $settings->logo
            ? Storage::disk('public')->path($settings->logo) : null;
        $stampPath     = $settings->stamp_image
            ? Storage::disk('public')->path($settings->stamp_image) : null;
        $signaturePath = $settings->signature_image
            ? Storage::disk('public')->path($settings->signature_image) : null;

        return Pdf::loadView('pdf.devis', compact(
            'quote', 'settings', 'logoPath', 'stampPath', 'signaturePath'
        ))
        ->setPaper('a4', 'portrait')
        ->download($quote->reference . '.pdf');
    }

    // ── Convertir devis en vente ──────────────────
    public function convertToSale(Quote $quote, array $payment): Sale
    {
        return DB::transaction(function () use ($quote, $payment) {
            $settings = CompanySetting::getSettings();

            // Récupérer ou créer le client
            $customerId = $quote->customer_id;
            if (!$customerId && $quote->customer_name) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $quote->customer_phone],
                    [
                        'name'     => $quote->customer_name,
                        'phone'    => $quote->customer_phone,
                        'whatsapp' => $quote->customer_phone,
                    ]
                );
                $customerId = $customer->id;
            }

            $amountPaid  = $payment['amount_paid'];
            $changeGiven = max(0, $amountPaid - $quote->total_ttc);

            // Créer la vente
            $lastSale = Sale::withTrashed()->latest()->first();
            $num      = $lastSale
                ? (intval(substr($lastSale->reference, -4)) + 1) : 1;
            $ref      = 'VNT-' . date('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'reference'      => $ref,
                'customer_id'    => $customerId,
                'user_id'        => Auth::id(),
                'source'         => 'shop',
                'status'         => 'completed',
                'subtotal_ht'    => $quote->subtotal_ht,
                'tva_amount'     => $quote->tva_amount,
                'total_ttc'      => $quote->total_ttc,
                'discount'       => $quote->discount,
                'amount_paid'    => $amountPaid,
                'change_given'   => $changeGiven,
                'payment_method' => $payment['method'],
                'notes'          => 'Converti depuis devis ' . $quote->reference,
                'sold_at'        => now(),
            ]);

            // Créer les lignes + décrémenter stock
            foreach ($quote->items as $item) {
                SaleItem::create([
                    'sale_id'        => $sale->id,
                    'product_id'     => $item->product_id,
                    'product_name'   => $item->description,
                    'unit_price_ht'  => $item->unit_price_ht,
                    'unit_price_ttc' => $item->unit_price_ttc,
                    'quantity'       => $item->quantity,
                    'discount'       => $item->discount,
                    'total_ht'       => $item->total_ht,
                    'total_ttc'      => $item->total_ttc,
                ]);

                if ($item->product_id) {
                    $product = \App\Models\Product::find($item->product_id);
                    if ($product) {
                        $this->stockService->decrementStock(
                            $product, $item->quantity,
                            'sale', $sale->id,
                            'Vente depuis devis #' . $quote->reference
                        );
                    }
                }
            }

            // Générer facture
            $invoice = \App\Models\Invoice::create([
                'sale_id'        => $sale->id,
                'customer_id'    => $customerId,
                'invoice_number' => $settings->invoice_prefix . '-' . date('Y') . '-'
                    . str_pad($settings->invoice_counter, 4, '0', STR_PAD_LEFT),
                'status'         => 'issued',
                'subtotal_ht'    => $quote->subtotal_ht,
                'tva_rate'       => $quote->tva_rate,
                'tva_amount'     => $quote->tva_amount,
                'total_ttc'      => $quote->total_ttc,
                'issued_at'      => now(),
            ]);
            $settings->increment('invoice_counter');

            // Marquer devis comme accepté
            $quote->update(['status' => 'accepted']);

            return $sale->load(['items', 'invoice', 'customer']);
        });
    }
}
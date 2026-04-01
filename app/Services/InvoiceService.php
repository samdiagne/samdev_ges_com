<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Invoice;
use App\Models\CompanySetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    // ── Générer PDF ───────────────────────────────
    public function generatePdf(Sale $sale): string
    {
        $sale->load(['items', 'customer', 'invoice']);

        $invoice  = $sale->invoice;
        $settings = CompanySetting::getSettings();

        // Résoudre les chemins absolus pour DomPDF
        $logoPath      = $settings->logo
            ? Storage::disk('public')->path($settings->logo)
            : null;
        $stampPath     = $settings->stamp_image
            ? Storage::disk('public')->path($settings->stamp_image)
            : null;
        $signaturePath = $settings->signature_image
            ? Storage::disk('public')->path($settings->signature_image)
            : null;

        $pdf = Pdf::loadView('pdf.facture', compact(
            'sale', 'invoice', 'settings',
            'logoPath', 'stampPath', 'signaturePath'
        ))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isRemoteEnabled'      => false,
            'isHtml5ParserEnabled' => true,
            'chroot'               => storage_path('app/public'),
        ]);

        $filename  = 'factures/' . $invoice->invoice_number . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());
        $invoice->update(['pdf_path' => $filename]);

        return $filename;
    }

    // ── Télécharger PDF ───────────────────────────
    public function download(Sale $sale)
    {
        $sale->load(['items', 'customer', 'invoice']);
        $invoice  = $sale->invoice;
        $settings = CompanySetting::getSettings();

        $logoPath      = $settings->logo
            ? Storage::disk('public')->path($settings->logo)
            : null;
        $stampPath     = $settings->stamp_image
            ? Storage::disk('public')->path($settings->stamp_image)
            : null;
        $signaturePath = $settings->signature_image
            ? Storage::disk('public')->path($settings->signature_image)
            : null;

        return Pdf::loadView('pdf.facture', compact(
            'sale', 'invoice', 'settings',
            'logoPath', 'stampPath', 'signaturePath'
        ))
        ->setPaper('a4', 'portrait')
        ->download($invoice->invoice_number . '.pdf');
    }

}
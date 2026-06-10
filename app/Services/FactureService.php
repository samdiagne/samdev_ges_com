<?php

namespace App\Services;

use App\Models\Vente;
use App\Models\Facture;
use App\Models\Parametre;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class FactureService
{
    /**
     * Générer le PDF de la facture et le stocker sur disque.
     * Retourne le chemin du fichier.
     */
    public function generatePdf(Vente $vente): string
    {
        $vente->load(['lignes', 'client', 'facture', 'user']);

        $facture    = $vente->facture;
        $parametres = Parametre::first();

        $logoPath      = $this->resoudreCheminAbsolu($parametres?->logo);
        $cachetPath    = $this->resoudreCheminAbsolu($parametres?->cachet);
        $signaturePath = $this->resoudreCheminAbsolu($parametres?->signature);

        $pdf = Pdf::loadView('pdf.facture', compact(
            'vente', 'facture', 'parametres',
            'logoPath', 'cachetPath', 'signaturePath'
        ))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isRemoteEnabled'      => false,
            'isHtml5ParserEnabled' => true,
            'chroot'               => storage_path('app/public'),
        ]);

        $filename = 'factures/' . $facture->numero_facture . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        $facture->update(['chemin_pdf' => $filename]);

        return $filename;
    }

    /**
     * Générer et retourner le PDF en téléchargement direct.
     */
    public function download(Vente $vente)
    {
        $vente->load(['lignes', 'client', 'facture', 'user']);

        $facture    = $vente->facture;
        $parametres = Parametre::first();

        $logoPath      = $this->resoudreCheminAbsolu($parametres?->logo);
        $cachetPath    = $this->resoudreCheminAbsolu($parametres?->cachet);
        $signaturePath = $this->resoudreCheminAbsolu($parametres?->signature);

        return Pdf::loadView('pdf.facture', compact(
            'vente', 'facture', 'parametres',
            'logoPath', 'cachetPath', 'signaturePath'
        ))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isRemoteEnabled'      => false,
            'isHtml5ParserEnabled' => true,
            'chroot'               => storage_path('app/public'),
        ])
        ->download($facture->numero_facture . '.pdf');
    }

    /**
     * Résoudre le chemin absolu d'un fichier stocké dans le disque public.
     * Nécessaire pour DomPDF qui ne peut pas accéder aux URLs distantes.
     */
    private function resoudreCheminAbsolu(?string $chemin): ?string
    {
        if (! $chemin) return null;

        $cheminAbsolu = Storage::disk('public')->path($chemin);

        return file_exists($cheminAbsolu) ? $cheminAbsolu : null;
    }
}

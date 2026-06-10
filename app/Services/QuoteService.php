<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Models\Facture;
use App\Models\Client;
use App\Models\Plat;
use App\Models\Parametre;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuoteService
{
    public function __construct(private StockService $stockService) {}

    public function generatePdf(Quote $quote): string
    {
        $quote->load(['items', 'client']);
        $parametres    = Parametre::first();
        $logoPath      = $this->resoudreCheminAbsolu($parametres?->logo);
        $cachetPath    = $this->resoudreCheminAbsolu($parametres?->cachet);
        $signaturePath = $this->resoudreCheminAbsolu($parametres?->signature);

        $pdf = Pdf::loadView('pdf.devis', compact(
            'quote', 'parametres', 'logoPath', 'cachetPath', 'signaturePath'
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
        $quote->load(['items', 'client']);
        $parametres    = Parametre::first();
        $logoPath      = $this->resoudreCheminAbsolu($parametres?->logo);
        $cachetPath    = $this->resoudreCheminAbsolu($parametres?->cachet);
        $signaturePath = $this->resoudreCheminAbsolu($parametres?->signature);

        return Pdf::loadView('pdf.devis', compact(
            'quote', 'parametres', 'logoPath', 'cachetPath', 'signaturePath'
        ))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont'          => 'DejaVu Sans',
            'isRemoteEnabled'      => false,
            'isHtml5ParserEnabled' => true,
            'chroot'               => storage_path('app/public'),
        ])
        ->download($quote->reference . '.pdf');
    }

    public function convertToSale(Quote $quote, array $payment): Vente
    {
        return DB::transaction(function () use ($quote, $payment) {
            $parametres = Parametre::first();

            $clientId = $quote->client_id;
            if (!$clientId && $quote->client_nom) {
                $client = Client::firstOrCreate(
                    ['telephone' => $quote->client_telephone],
                    ['nom' => $quote->client_nom, 'telephone' => $quote->client_telephone]
                );
                $clientId = $client->id;
            }

            $amountPaid  = $payment['amount_paid'];
            $changeGiven = max(0, $amountPaid - $quote->total);

            $lastSale = Vente::withTrashed()->latest()->first();
            $num      = $lastSale
                ? (intval(substr($lastSale->reference, -4)) + 1) : 1;
            $ref      = 'VNT-' . date('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);

            $vente = Vente::create([
                'reference'      => $ref,
                'client_id'      => $clientId,
                'user_id'        => Auth::id(),
                'source'         => 'devis',
                'statut'         => 'completee',
                'total'          => $quote->total,
                'remise'         => $quote->remise,
                'montant_recu'   => $amountPaid,
                'monnaie_rendue' => $changeGiven,
                'mode_paiement'  => $payment['method'],
                'notes'          => 'Converti depuis devis ' . $quote->reference,
                'vendu_le'       => now(),
            ]);

            foreach ($quote->items as $item) {
                VenteLigne::create([
                    'vente_id'      => $vente->id,
                    'plat_id'       => $item->plat_id,
                    'nom_plat'      => $item->description,
                    'prix_unitaire' => $item->prix_unitaire,
                    'quantite'      => $item->quantite,
                    'remise'        => $item->remise,
                    'total'         => $item->total,
                ]);

                if ($item->plat_id) {
                    $plat = Plat::find($item->plat_id);
                    if ($plat) {
                        $this->stockService->decrementerStock(
                            $plat, $item->quantite,
                            'vente', $vente->id,
                            'Vente depuis devis #' . $quote->reference
                        );
                    }
                }
            }

            Facture::create([
                'vente_id'       => $vente->id,
                'client_id'      => $clientId,
                'numero_facture' => ($parametres?->prefixe_facture ?? 'FAC') . '-' . date('Y') . '-'
                    . str_pad($parametres?->compteur_facture ?? 1, 4, '0', STR_PAD_LEFT),
                'statut'         => 'emise',
                'total'          => $quote->total,
                'emise_le'       => now(),
            ]);
            $parametres?->increment('compteur_facture');

            $quote->update(['status' => 'accepted']);

            return $vente->load(['lignes', 'facture', 'client']);
        });
    }

    private function resoudreCheminAbsolu(?string $chemin): ?string
    {
        if (! $chemin) return null;
        $cheminAbsolu = Storage::disk('public')->path($chemin);
        return file_exists($cheminAbsolu) ? $cheminAbsolu : null;
    }
}

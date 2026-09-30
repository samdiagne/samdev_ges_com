<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Services\FactureService;

class FactureController extends Controller
{
    public function __construct(private FactureService $factureService) {}

    public function show(Vente $vente)
    {
        $vente->load(['lignes.produit', 'client', 'facture', 'user']);

        if ($vente->facture) {
            $this->factureService->generatePdf($vente);
        }

        return view('factures.show', compact('vente'));
    }

    public function download(Vente $vente)
    {
        $vente->load(['lignes', 'client', 'facture']);
        return $this->factureService->download($vente);
    }

    public function toggleStatut(Vente $vente)
    {
        if ($vente->facture) {
            $this->factureService->basculerStatut($vente->facture);
            $msg = $vente->facture->estPayee()
                ? 'La facture est désormais marquée comme Payée (Acquittée).'
                : 'La facture est désormais marquée comme À régler.';
            return back()->with('success', $msg);
        }
        return back()->with('error', 'Aucune facture associée.');
    }
}

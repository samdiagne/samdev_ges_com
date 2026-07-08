<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Depense;
use App\Models\Achat;
use App\Models\Produit;
use App\Models\Parametre;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EtatController extends Controller
{
    // ── Helper période ─────────────────────────────
    // Gere les parametres absents OU vides, et remet les bornes
    // dans l'ordre si l'utilisateur les inverse.
    private function getPeriode(Request $request): array
    {
        $from = Carbon::parse($request->filled('date_from') ? $request->date_from : now()->startOfMonth())->startOfDay();
        $to   = Carbon::parse($request->filled('date_to') ? $request->date_to : now())->endOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
    }

    // ── État des ventes ───────────────────────────
    public function etatVentes(Request $request)
    {
        [$from, $to] = $this->getPeriode($request);

        $ventes = Vente::with(['client', 'lignes'])
            ->where('statut', 'completee')
            ->whereBetween('vendu_le', [$from, $to])
            ->latest('vendu_le')
            ->get();

        $stats = [
            'total'         => $ventes->sum('total'),
            'count'         => $ventes->count(),
            'cash'          => $ventes->where('mode_paiement', 'cash')->sum('total'),
            'wave'          => $ventes->where('mode_paiement', 'wave')->sum('total'),
            'om'            => $ventes->where('mode_paiement', 'om')->sum('total'),
            'carte'         => $ventes->where('mode_paiement', 'carte')->sum('total'),
            'panier_moyen'  => $ventes->count() > 0
                ? round($ventes->sum('total') / $ventes->count()) : 0,
        ];

        $parametres = Parametre::first();
        $logoBase64 = $this->getLogoBase64();

        return Pdf::loadView('pdf.etats.etat-ventes',
            compact('ventes', 'stats', 'parametres', 'logoBase64', 'from', 'to')
        )
        ->setPaper('a4', 'portrait')
        ->setOptions(['defaultFont' => 'DejaVu Sans', 'isHtml5ParserEnabled' => true])
        ->download('etat-ventes-' . $from->format('Y-m-d') . '.pdf');
    }

    // ── État des dépenses ─────────────────────────
    public function etatDepenses(Request $request)
    {
        [$from, $to] = $this->getPeriode($request);

        $depenses = Depense::with(['categorie', 'user'])
            ->whereBetween('date_depense', [$from->toDateString(), $to->toDateString()])
            ->latest('date_depense')
            ->get();

        $stats = [
            'total'         => $depenses->sum('montant'),
            'count'         => $depenses->count(),
            'par_categorie' => $depenses->groupBy('categorie.nom')
                ->map(fn($g) => $g->sum('montant')),
        ];

        $parametres = Parametre::first();
        $logoBase64 = $this->getLogoBase64();

        return Pdf::loadView('pdf.etats.etat-depenses',
            compact('depenses', 'stats', 'parametres', 'logoBase64', 'from', 'to')
        )
        ->setPaper('a4', 'portrait')
        ->setOptions(['defaultFont' => 'DejaVu Sans', 'isHtml5ParserEnabled' => true])
        ->download('etat-depenses-' . $from->format('Y-m-d') . '.pdf');
    }

    // ── État des achats ───────────────────────────
    public function etatAchats(Request $request)
    {
        [$from, $to] = $this->getPeriode($request);

        $achats = Achat::with(['fournisseur', 'lignes'])
            ->whereBetween('date_achat', [$from->toDateString(), $to->toDateString()])
            ->latest('date_achat')
            ->get();

        $stats = [
            'total'           => $achats->sum('montant_total'),
            'count'           => $achats->count(),
            'paye'            => $achats->sum('montant_paye'),
            'du'              => $achats->sum('montant_du'),
            'par_fournisseur' => $achats->groupBy(fn($a) => $a->fournisseur?->nom ?? 'Sans fournisseur')
                ->map(fn($g) => $g->sum('montant_total'))
                ->sortDesc(),
        ];

        $parametres = Parametre::first();
        $logoBase64 = $this->getLogoBase64();

        return Pdf::loadView('pdf.etats.etat-achats',
            compact('achats', 'stats', 'parametres', 'logoBase64', 'from', 'to')
        )
        ->setPaper('a4', 'portrait')
        ->setOptions(['defaultFont' => 'DejaVu Sans', 'isHtml5ParserEnabled' => true])
        ->download('etat-achats-' . $from->format('Y-m-d') . '.pdf');
    }

    // ── État croisé achats + dépenses ─────────────
    public function etatAchatsDepenses(Request $request)
    {
        [$from, $to] = $this->getPeriode($request);

        $achats = Achat::with('fournisseur')
            ->whereBetween('date_achat', [$from->toDateString(), $to->toDateString()])
            ->get();

        $depenses = Depense::with('categorie')
            ->whereBetween('date_depense', [$from->toDateString(), $to->toDateString()])
            ->get();

        // Fusion chronologique des deux types de sorties
        $mouvements = $achats->map(fn($a) => [
                'date'    => Carbon::parse($a->date_achat),
                'type'    => 'achat',
                'libelle' => $a->reference . ($a->fournisseur ? ' — ' . $a->fournisseur->nom : ''),
                'mode'    => $a->mode_paiement,
                'montant' => (float) $a->montant_total,
                'du'      => (float) $a->montant_du,
            ])
            ->concat($depenses->map(fn($d) => [
                'date'    => Carbon::parse($d->date_depense),
                'type'    => 'depense',
                'libelle' => ($d->categorie?->nom ? $d->categorie->nom . ' — ' : '') . $d->description,
                'mode'    => $d->mode_paiement,
                'montant' => (float) $d->montant,
                'du'      => 0.0,
            ]))
            ->sortBy('date')
            ->values();

        $stats = [
            'achats_total'   => $achats->sum('montant_total'),
            'achats_count'   => $achats->count(),
            'achats_du'      => $achats->sum('montant_du'),
            'depenses_total' => $depenses->sum('montant'),
            'depenses_count' => $depenses->count(),
            'total_sorties'  => $achats->sum('montant_total') + $depenses->sum('montant'),
        ];

        $parametres = Parametre::first();
        $logoBase64 = $this->getLogoBase64();

        return Pdf::loadView('pdf.etats.etat-achats-depenses',
            compact('mouvements', 'stats', 'parametres', 'logoBase64', 'from', 'to')
        )
        ->setPaper('a4', 'portrait')
        ->setOptions(['defaultFont' => 'DejaVu Sans', 'isHtml5ParserEnabled' => true])
        ->download('etat-achats-depenses-' . $from->format('Y-m-d') . '.pdf');
    }

    // ── État de trésorerie ────────────────────────
    public function etatTresorerie(Request $request)
    {
        [$from, $to] = $this->getPeriode($request);

        $recettes = Vente::where('statut', 'completee')
            ->whereBetween('vendu_le', [$from, $to])
            ->sum('total');

        $achats = Achat::whereBetween('date_achat', [
            $from->toDateString(), $to->toDateString()
        ])->sum('montant_paye');

        $depenses = Depense::whereBetween('date_depense', [
            $from->toDateString(), $to->toDateString()
        ])->sum('montant');

        $solde = $recettes - $achats - $depenses;

        // Mouvements journaliers
        $mouvements = [];
        $current = $from->copy();
        while ($current->lte($to)) {
            $dateStr = $current->toDateString();
            $recette = Vente::where('statut', 'completee')
                ->whereDate('vendu_le', $dateStr)->sum('total');
            $achat   = Achat::whereDate('date_achat', $dateStr)->sum('montant_paye');
            $depense = Depense::whereDate('date_depense', $dateStr)->sum('montant');

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

        $stats      = compact('recettes', 'achats', 'depenses', 'solde');
        $parametres = Parametre::first();
        $logoBase64 = $this->getLogoBase64();

        return Pdf::loadView('pdf.etats.etat-tresorerie',
            compact('mouvements', 'stats', 'parametres', 'logoBase64', 'from', 'to')
        )
        ->setPaper('a4', 'portrait')
        ->setOptions(['defaultFont' => 'DejaVu Sans', 'isHtml5ParserEnabled' => true])
        ->download('etat-tresorerie-' . $from->format('Y-m-d') . '.pdf');
    }

    // ── Bilan stocks ──────────────────────────────
    public function bilanStocks(Request $request)
    {
        $produits = Produit::with('categorie')
            ->where('est_actif', true)
            ->orderBy('categorie_id')
            ->orderBy('nom')
            ->get();

        $stats = [
            'total_produits' => $produits->count(),
            'valeur_vente'   => $produits->sum(fn($p) => $p->stock * $p->prix_vente),
            'ruptures'       => $produits->where('stock', '<=', 0)->where('sur_commande', false)->count(),
            'stock_bas'      => $produits->filter(fn($p) =>
                ! $p->sur_commande && $p->stock > 0 && $p->stock <= $p->seuil_alerte_stock
            )->count(),
        ];

        $parametres = Parametre::first();
        $logoBase64 = $this->getLogoBase64();

        return Pdf::loadView('pdf.etats.bilan-stocks',
            compact('produits', 'stats', 'parametres', 'logoBase64')
        )
        ->setPaper('a4', 'landscape')
        ->setOptions(['defaultFont' => 'DejaVu Sans', 'isHtml5ParserEnabled' => true])
        ->download('bilan-stocks-' . now()->format('Y-m-d') . '.pdf');
    }

    // ── Helper logo ───────────────────────────────
    private function getLogoBase64(): ?string
    {
        $parametres = Parametre::first();
        if (! $parametres?->logo) return null;

        $path = Storage::disk('public')->path($parametres->logo);
        if (! file_exists($path)) return null;

        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = base64_encode(file_get_contents($path));
        return "data:image/{$type};base64,{$data}";
    }
}

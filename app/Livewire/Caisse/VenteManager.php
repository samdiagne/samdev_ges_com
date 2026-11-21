<?php

namespace App\Livewire\Caisse;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Vente;
use App\Models\VenteLigne;
use App\Models\Client;
use App\Models\Produit;
use App\Models\Facture;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VenteManager extends Component
{
    use WithPagination;

    public string $search         = '';
    public string $filtreDate     = '';
    public string $filtreMonth    = '';
    public string $filtreSource   = '';
    public string $filtrePaiement = '';
    public string $dateFrom       = '';
    public string $dateTo         = '';

    public bool   $afficherModalDetail = false;
    public ?int   $voirVenteId         = null;

    // ── Propriétés Sélection Multiple (Checkboxes) ──
    public array  $selectedVentes                    = [];
    public bool   $selectAll                         = false;
    public bool   $afficherModalSupprimerSelection   = false;

    // ── Propriétés Suppression (Admin) ─────────────
    public bool   $afficherModalSupprimer = false;
    public ?int   $supprimerVenteId       = null;
    public ?Vente $venteASupprimer        = null;

    // ── Propriétés Modification (Admin) ───────────
    public bool   $afficherModalModifier = false;
    public ?int   $modifierVenteId       = null;
    public string $editReference         = '';
    public ?int   $editClientId          = null;
    public string $editDate              = '';
    public string $editModePaiement      = 'cash';
    public float  $editRemise            = 0;
    public float  $editMontantRecu       = 0;
    public ?string $editNotes            = '';
    public array  $editLignes            = [];

    public string $searchAjoutProduit    = '';
    public array  $produitsDisponibles   = [];

    public float  $editSousTotal         = 0;
    public float  $editTotal             = 0;
    public float  $editMonnaieRendue     = 0;

    public function mount(): void
    {
        $this->filtreMonth = now()->format('Y-m');
    }

    public function updatingSearch(): void { $this->resetPage(); $this->clearSelection(); }
    public function updatingFiltreDate(): void {
        $this->resetPage();
        $this->clearSelection();
        // Si l'utilisateur choisit une date précise, synchroniser le mois correspondant
        if ($this->filtreDate) {
            $this->filtreMonth = substr($this->filtreDate, 0, 7);
        }
    }
    public function updatingFiltreMonth(): void {
        $this->resetPage();
        $this->clearSelection();
        // Si l'utilisateur change le mois manuellement et qu'une date précise était sélectionnée mais hors du mois
        if ($this->filtreDate && ! str_starts_with($this->filtreDate, $this->filtreMonth)) {
            $this->filtreDate = '';
        }
    }
    public function updatingFiltreSource(): void { $this->resetPage(); $this->clearSelection(); }
    public function updatingFiltrePaiement(): void { $this->resetPage(); $this->clearSelection(); }
    public function updatingDateFrom(): void { $this->resetPage(); $this->clearSelection(); }
    public function updatingDateTo(): void { $this->resetPage(); $this->clearSelection(); }

    public function filtrerAujourdhui(): void
    {
        $this->filtreDate = now()->toDateString();
        $this->filtreMonth = now()->format('Y-m');
        $this->resetPage();
        $this->clearSelection();
    }

    public function reinitialiserDate(): void
    {
        $this->filtreDate = '';
        $this->resetPage();
        $this->clearSelection();
    }

    // ── Gestion de la sélection multiple ───────────
    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $currentPageIds = $this->getVentesQuery()->paginate(15)->pluck('id')->toArray();
            $this->selectedVentes = array_values(array_unique(array_map('strval', $currentPageIds)));
        } else {
            $this->selectedVentes = [];
        }
    }

    public function clearSelection(): void
    {
        $this->selectedVentes = [];
        $this->selectAll = false;
    }

    public function getTotalSelectionProperty(): float
    {
        if (empty($this->selectedVentes)) {
            return 0;
        }
        return (float) Vente::whereIn('id', array_map('intval', $this->selectedVentes))->sum('total');
    }

    public function confirmerSuppressionSelection(): void
    {
        if (! auth()->user()?->isAdmin()) {
            session()->flash('error', 'Action non autorisée. Seul un administrateur peut supprimer des ventes.');
            return;
        }

        if (empty($this->selectedVentes)) {
            return;
        }

        $this->afficherModalSupprimerSelection = true;
    }

    public function annulerSuppressionSelection(): void
    {
        $this->afficherModalSupprimerSelection = false;
    }

    public function supprimerSelection(): void
    {
        if (! auth()->user()?->isAdmin()) {
            session()->flash('error', 'Action non autorisée. Seul un administrateur peut supprimer des ventes.');
            return;
        }

        if (empty($this->selectedVentes)) {
            return;
        }

        $ids = array_map('intval', $this->selectedVentes);
        $count = count($ids);

        DB::transaction(function () use ($ids) {
            $stockService = app(StockService::class);
            $ventes = Vente::with(['lignes.produit', 'facture'])->whereIn('id', $ids)->get();

            foreach ($ventes as $vente) {
                // Réintégrer les stocks pour chaque ligne
                foreach ($vente->lignes as $ligne) {
                    if ($ligne->produit) {
                        $stockService->incrementerStock(
                            $ligne->produit,
                            $ligne->quantite,
                            'annulation_vente',
                            $vente->id,
                            'Suppression groupée Vente #' . $vente->reference
                        );
                    }
                }

                // Supprimer la facture
                if ($vente->facture) {
                    $vente->facture->delete();
                }

                // Supprimer les lignes et la vente
                $vente->lignes()->delete();
                $vente->delete();
            }
        });

        $this->clearSelection();
        $this->afficherModalSupprimerSelection = false;
        session()->flash('success', "{$count} vente(s) supprimée(s) avec succès et les stocks ont été réintégrés.");
    }

    public function view(int $id): void
    {
        $this->voirVenteId         = $id;
        $this->afficherModalDetail = true;
    }

    // ── Logique Suppression (Admin uniquement) ────
    public function confirmerSuppression(int $id): void
    {
        if (! auth()->user()?->isAdmin()) {
            session()->flash('error', 'Action non autorisée. Seul un administrateur peut supprimer une vente.');
            return;
        }

        $this->supprimerVenteId       = $id;
        $this->venteASupprimer        = Vente::with(['client', 'lignes.produit'])->find($id);
        $this->afficherModalSupprimer = true;
    }

    public function annulerSuppression(): void
    {
        $this->afficherModalSupprimer = false;
        $this->supprimerVenteId       = null;
        $this->venteASupprimer        = null;
    }

    public function supprimerVente(): void
    {
        if (! auth()->user()?->isAdmin()) {
            session()->flash('error', 'Action non autorisée. Seul un administrateur peut supprimer une vente.');
            return;
        }

        if (! $this->supprimerVenteId) {
            return;
        }

        $vente = Vente::with(['lignes.produit', 'facture'])->findOrFail($this->supprimerVenteId);
        $reference = $vente->reference;

        DB::transaction(function () use ($vente) {
            $stockService = app(StockService::class);

            // Réintégrer les stocks pour chaque ligne vendue
            foreach ($vente->lignes as $ligne) {
                if ($ligne->produit) {
                    $stockService->incrementerStock(
                        $ligne->produit,
                        $ligne->quantite,
                        'annulation_vente',
                        $vente->id,
                        'Suppression Vente #' . $vente->reference
                    );
                }
            }

            // Supprimer la facture associée si présente
            if ($vente->facture) {
                $vente->facture->delete();
            }

            // Supprimer les lignes et la vente
            $vente->lignes()->delete();
            $vente->delete();
        });

        $this->annulerSuppression();
        if ($this->afficherModalDetail && $this->voirVenteId === $vente->id) {
            $this->afficherModalDetail = false;
            $this->voirVenteId = null;
        }

        session()->flash('success', "La vente #{$reference} a été supprimée avec succès et les stocks ont été réintégrés.");
    }

    // ── Logique Modification (Admin uniquement) ───
    public function edit(int $id): void
    {
        if (! auth()->user()?->isAdmin()) {
            session()->flash('error', 'Action non autorisée. Seul un administrateur peut modifier une vente.');
            return;
        }

        $vente = Vente::with(['lignes.produit', 'client'])->findOrFail($id);

        $this->modifierVenteId  = $id;
        $this->editReference    = $vente->reference;
        $this->editClientId     = $vente->client_id;
        $this->editDate         = $vente->vendu_le ? $vente->vendu_le->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i');
        $this->editModePaiement = $vente->mode_paiement;
        $this->editRemise       = (float) $vente->remise;
        $this->editMontantRecu  = (float) $vente->montant_recu;
        $this->editNotes        = $vente->notes ?? '';

        $this->editLignes = $vente->lignes->map(fn($l) => [
            'id'            => $l->id,
            'produit_id'    => $l->produit_id,
            'nom_produit'   => $l->nom_produit,
            'prix_unitaire' => (float) $l->prix_unitaire,
            'quantite'      => (int) $l->quantite,
            'old_quantite'  => (int) $l->quantite, // Mémorisé pour le calcul de delta de stock
            'remise'        => (float) $l->remise,
            'total'         => (float) $l->total,
        ])->toArray();

        $this->recalculerEditTotaux();
        $this->afficherModalModifier = true;
    }

    public function annulerModification(): void
    {
        $this->afficherModalModifier = false;
        $this->modifierVenteId       = null;
        $this->editLignes            = [];
        $this->searchAjoutProduit    = '';
        $this->produitsDisponibles   = [];
    }

    public function updateEditQuantite(int $index, $qty): void
    {
        $qty = (int) $qty;
        if ($qty <= 0) {
            $this->removeEditLigne($index);
            return;
        }

        if (isset($this->editLignes[$index])) {
            $this->editLignes[$index]['quantite'] = $qty;
            $this->recalculerEditLigne($index);
            $this->recalculerEditTotaux();
        }
    }

    public function removeEditLigne(int $index): void
    {
        if (isset($this->editLignes[$index])) {
            array_splice($this->editLignes, $index, 1);
            $this->editLignes = array_values($this->editLignes);
            $this->recalculerEditTotaux();
        }
    }

    public function updatedSearchAjoutProduit(): void
    {
        if (strlen(trim($this->searchAjoutProduit)) < 2) {
            $this->produitsDisponibles = [];
            return;
        }

        $this->produitsDisponibles = Produit::where('est_actif', true)
            ->where('nom', 'like', '%' . trim($this->searchAjoutProduit) . '%')
            ->limit(6)
            ->get(['id', 'nom', 'prix_vente', 'stock'])
            ->toArray();
    }

    public function addProduitToEdit(int $produitId): void
    {
        // Si le produit existe déjà dans les lignes modifiées
        foreach ($this->editLignes as $idx => $item) {
            if (($item['produit_id'] ?? null) === $produitId) {
                $this->updateEditQuantite($idx, $item['quantite'] + 1);
                $this->searchAjoutProduit  = '';
                $this->produitsDisponibles = [];
                return;
            }
        }

        $produit = Produit::findOrFail($produitId);

        $this->editLignes[] = [
            'id'            => null,
            'produit_id'    => $produit->id,
            'nom_produit'   => $produit->nom,
            'prix_unitaire' => (float) $produit->prix_vente,
            'quantite'      => 1,
            'old_quantite'  => 0, // Nouveau produit ajouté
            'remise'        => 0,
            'total'         => (float) $produit->prix_vente,
        ];

        $this->searchAjoutProduit  = '';
        $this->produitsDisponibles = [];
        $this->recalculerEditTotaux();
    }

    private function recalculerEditLigne(int $index): void
    {
        if (!isset($this->editLignes[$index])) return;
        $l = $this->editLignes[$index];
        $this->editLignes[$index]['total'] = round($l['prix_unitaire'] * $l['quantite'] - ($l['remise'] ?? 0), 2);
    }

    public function recalculerEditTotaux(): void
    {
        $this->editSousTotal = round(array_sum(array_column($this->editLignes, 'total')), 2);
        $this->editTotal     = max(0, round($this->editSousTotal - $this->editRemise, 2));

        if ($this->editModePaiement === 'a_regler') {
            $this->editMontantRecu   = 0;
            $this->editMonnaieRendue = 0;
        } else {
            if ($this->editMontantRecu < $this->editTotal) {
                $this->editMontantRecu = $this->editTotal;
            }
            $this->editMonnaieRendue = max(0, round($this->editMontantRecu - $this->editTotal, 2));
        }
    }

    public function updatedEditRemise(): void { $this->recalculerEditTotaux(); }
    public function updatedEditMontantRecu(): void { $this->recalculerEditTotaux(); }
    public function updatedEditModePaiement(): void { $this->recalculerEditTotaux(); }

    public function updateVente(): void
    {
        if (! auth()->user()?->isAdmin()) {
            session()->flash('error', 'Action non autorisée. Seul un administrateur peut modifier une vente.');
            return;
        }

        if (empty($this->editLignes)) {
            $this->addError('editLignes', 'La vente doit comporter au moins un article.');
            return;
        }

        $vente = Vente::with(['lignes.produit', 'facture'])->findOrFail($this->modifierVenteId);
        $stockService = app(StockService::class);

        DB::transaction(function () use ($vente, $stockService) {
            // 1. Gestion des stocks
            $anciennesLignes = $vente->lignes->keyBy('produit_id');
            $nouvellesLignesByProduit = collect($this->editLignes)->filter(fn($l) => !empty($l['produit_id']))->keyBy('produit_id');

            // Articles supprimés de la vente -> réintégration en stock
            foreach ($anciennesLignes as $produitId => $oldLigne) {
                if (!$nouvellesLignesByProduit->has($produitId) && $oldLigne->produit) {
                    $stockService->incrementerStock(
                        $oldLigne->produit,
                        $oldLigne->quantite,
                        'modification_vente',
                        $vente->id,
                        'Retrait article suite modification Vente #' . $vente->reference
                    );
                }
            }

            // Articles existants ou nouveaux -> calcul du delta
            foreach ($this->editLignes as $newLine) {
                $produitId = $newLine['produit_id'];
                if (!$produitId) continue;

                $produit = Produit::find($produitId);
                if (!$produit) continue;

                $oldQty = $anciennesLignes->has($produitId) ? $anciennesLignes[$produitId]->quantite : 0;
                $newQty = (int) $newLine['quantite'];
                $delta  = $newQty - $oldQty;

                if ($delta > 0) {
                    // Augmentation de quantité -> sortie de stock
                    $stockService->decrementerStock(
                        $produit,
                        $delta,
                        'modification_vente',
                        $vente->id,
                        'Augmentation quantité suite modification Vente #' . $vente->reference
                    );
                } elseif ($delta < 0) {
                    // Diminution de quantité -> rentrée en stock
                    $stockService->incrementerStock(
                        $produit,
                        abs($delta),
                        'modification_vente',
                        $vente->id,
                        'Diminution quantité suite modification Vente #' . $vente->reference
                    );
                }
            }

            // 2. Mettre à jour les lignes de vente
            $vente->lignes()->delete();
            foreach ($this->editLignes as $item) {
                VenteLigne::create([
                    'vente_id'      => $vente->id,
                    'produit_id'    => $item['produit_id'] ?? null,
                    'nom_produit'   => $item['nom_produit'],
                    'prix_unitaire' => $item['prix_unitaire'],
                    'quantite'      => $item['quantite'],
                    'remise'        => $item['remise'] ?? 0,
                    'total'         => $item['total'],
                ]);
            }

            // 3. Mettre à jour la vente
            $estARegler = ($this->editModePaiement === 'a_regler');
            $venduLe = $this->editDate ? Carbon::parse($this->editDate) : $vente->vendu_le;

            $vente->update([
                'client_id'      => $this->editClientId ?: null,
                'statut'         => $estARegler ? 'en_attente' : 'completee',
                'mode_paiement'  => $this->editModePaiement,
                'total'          => $this->editTotal,
                'remise'         => $this->editRemise,
                'montant_recu'   => $estARegler ? 0 : $this->editMontantRecu,
                'monnaie_rendue' => $estARegler ? 0 : $this->editMonnaieRendue,
                'notes'          => $this->editNotes ?: null,
                'vendu_le'       => $venduLe,
            ]);

            // 4. Mettre à jour la facture si elle existe
            if ($vente->facture) {
                $vente->facture->update([
                    'client_id' => $this->editClientId ?: null,
                    'statut'    => $estARegler ? Facture::STATUT_A_REGLER : Facture::STATUT_PAYEE,
                    'total'     => $this->editTotal,
                    'emise_le'  => $venduLe,
                ]);
            }
        });

        $reference = $vente->reference;
        $this->annulerModification();

        if ($this->afficherModalDetail && $this->voirVenteId === $vente->id) {
            $this->voirVenteId = $vente->id;
        }

        session()->flash('success', "La vente #{$reference} a été modifiée avec succès et les stocks ont été ajustés.");
    }

    protected function getVentesQuery()
    {
        return Vente::with(['client', 'user', 'lignes', 'facture'])
            ->when($this->search, fn($q) =>
                $q->where('reference', 'like', '%' . $this->search . '%')
                  ->orWhereHas('client', fn($q2) =>
                      $q2->where('nom', 'like', '%' . $this->search . '%')
                  )
            )
            ->when($this->filtreSource, fn($q) =>
                $q->where('source', $this->filtreSource)
            )
            ->when($this->filtrePaiement, fn($q) =>
                $q->where('mode_paiement', $this->filtrePaiement)
            )
            ->when($this->filtreDate, fn($q) =>
                $q->whereDate('vendu_le', $this->filtreDate)
            )
            ->when(! $this->filtreDate && $this->filtreMonth && ! $this->dateFrom, fn($q) =>
                $q->whereYear('vendu_le', substr($this->filtreMonth, 0, 4))
                  ->whereMonth('vendu_le', substr($this->filtreMonth, 5, 2))
            )
            ->when($this->dateFrom, fn($q) => $q->whereDate('vendu_le', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('vendu_le', '<=', $this->dateTo))
            ->latest('vendu_le');
    }

    public function render()
    {
        $ventes = $this->getVentesQuery()->paginate(15);

        $statsQuery = Vente::where('statut', 'completee')
            ->when($this->filtreDate, fn($q) =>
                $q->whereDate('vendu_le', $this->filtreDate)
            )
            ->when(! $this->filtreDate && $this->filtreMonth && ! $this->dateFrom, fn($q) =>
                $q->whereYear('vendu_le', substr($this->filtreMonth, 0, 4))
                  ->whereMonth('vendu_le', substr($this->filtreMonth, 5, 2))
            );

        $stats = [
            'count' => $statsQuery->count(),
            'total' => $statsQuery->sum('total'),
            'cash'  => (clone $statsQuery)->where('mode_paiement', 'cash')->sum('total'),
            'wave'  => (clone $statsQuery)->where('mode_paiement', 'wave')->sum('total'),
            'om'    => (clone $statsQuery)->where('mode_paiement', 'om')->sum('total'),
            'carte' => (clone $statsQuery)->where('mode_paiement', 'carte')->sum('total'),
        ];

        $voirVente = $this->voirVenteId
            ? Vente::with(['client', 'user', 'lignes.produit', 'facture'])->find($this->voirVenteId)
            : null;

        $clients = Client::orderBy('nom')->get(['id', 'nom', 'telephone']);

        return view('livewire.caisse.vente-manager', compact('ventes', 'stats', 'voirVente', 'clients'));
    }
}

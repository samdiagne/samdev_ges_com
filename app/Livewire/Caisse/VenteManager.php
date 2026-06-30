<?php

namespace App\Livewire\Caisse;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Vente;

class VenteManager extends Component
{
    use WithPagination;

    public string $search         = '';
    public string $filtreMonth    = '';
    public string $filtreSource   = '';
    public string $filtrePaiement = '';
    public string $dateFrom       = '';
    public string $dateTo         = '';

    public bool   $afficherModalDetail = false;
    public ?int   $voirVenteId         = null;

    public function mount(): void
    {
        $this->filtreMonth = now()->format('Y-m');
    }

    public function updatingSearch(): void { $this->resetPage(); }

    public function view(int $id): void
    {
        $this->voirVenteId         = $id;
        $this->afficherModalDetail = true;
    }

    public function render()
    {
        $ventes = Vente::with(['client', 'user', 'lignes', 'facture'])
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
            ->when($this->filtreMonth && ! $this->dateFrom, fn($q) =>
                $q->whereYear('vendu_le', substr($this->filtreMonth, 0, 4))
                  ->whereMonth('vendu_le', substr($this->filtreMonth, 5, 2))
            )
            ->when($this->dateFrom, fn($q) => $q->whereDate('vendu_le', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('vendu_le', '<=', $this->dateTo))
            ->latest('vendu_le')
            ->paginate(15);

        $statsQuery = Vente::where('statut', 'completee')
            ->when($this->filtreMonth && ! $this->dateFrom, fn($q) =>
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

        return view('livewire.caisse.vente-manager', compact('ventes', 'stats', 'voirVente'));
    }
}

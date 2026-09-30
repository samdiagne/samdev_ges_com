<?php

namespace App\Livewire\Factures;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Facture;

class FactureManager extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $filtreMonth  = '';
    public string $filtreStatut = '';

    public function mount(): void
    {
        $this->filtreMonth = now()->format('Y-m');
    }

    public function updatingSearch(): void       { $this->resetPage(); }
    public function updatingFiltreStatut(): void { $this->resetPage(); }
    public function updatingFiltreMonth(): void  { $this->resetPage(); }

    public function toggleStatut(int $factureId, \App\Services\FactureService $factureService): void
    {
        $facture = Facture::findOrFail($factureId);
        $factureService->basculerStatut($facture);
        session()->flash('success', 'Statut de la facture ' . $facture->numero_facture . ' mis à jour (' . ($facture->estPayee() ? 'Payée' : 'À régler') . ').');
    }

    public function render()
    {
        $baseQuery = Facture::when($this->filtreMonth, fn($q) =>
            $q->whereYear('emise_le', substr($this->filtreMonth, 0, 4))
              ->whereMonth('emise_le', substr($this->filtreMonth, 5, 2))
        );

        $factures = Facture::with(['vente.client', 'vente.user'])
            ->when($this->search, fn($q) =>
                $q->where('numero_facture', 'like', '%' . $this->search . '%')
                  ->orWhereHas('vente.client', fn($q2) =>
                      $q2->where('nom', 'like', '%' . $this->search . '%')
                  )
            )
            ->when($this->filtreMonth, fn($q) =>
                $q->whereYear('emise_le', substr($this->filtreMonth, 0, 4))
                  ->whereMonth('emise_le', substr($this->filtreMonth, 5, 2))
            )
            ->when($this->filtreStatut === 'payee', fn($q) =>
                $q->where('statut', Facture::STATUT_PAYEE)
            )
            ->when($this->filtreStatut === 'a_regler', fn($q) =>
                $q->whereIn('statut', [Facture::STATUT_A_REGLER, 'emise'])
            )
            ->latest('emise_le')
            ->paginate(15);

        $stats = [
            'total'     => (clone $baseQuery)->sum('total'),
            'total_paye' => (clone $baseQuery)->where('statut', Facture::STATUT_PAYEE)->sum('total'),
            'total_due'  => (clone $baseQuery)->whereIn('statut', [Facture::STATUT_A_REGLER, 'emise'])->sum('total'),
            'count'     => (clone $baseQuery)->count(),
            'count_paye' => (clone $baseQuery)->where('statut', Facture::STATUT_PAYEE)->count(),
            'count_due'  => (clone $baseQuery)->whereIn('statut', [Facture::STATUT_A_REGLER, 'emise'])->count(),
        ];

        return view('livewire.factures.facture-manager', compact('factures', 'stats'));
    }
}

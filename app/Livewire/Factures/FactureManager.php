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

    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
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
            ->latest('emise_le')
            ->paginate(15);

        $stats = [
            'total' => Facture::when($this->filtreMonth, fn($q) =>
                $q->whereYear('emise_le', substr($this->filtreMonth, 0, 4))
                  ->whereMonth('emise_le', substr($this->filtreMonth, 5, 2))
            )->sum('total'),
            'count' => Facture::when($this->filtreMonth, fn($q) =>
                $q->whereYear('emise_le', substr($this->filtreMonth, 0, 4))
                  ->whereMonth('emise_le', substr($this->filtreMonth, 5, 2))
            )->count(),
        ];

        return view('livewire.factures.facture-manager', compact('factures', 'stats'));
    }
}

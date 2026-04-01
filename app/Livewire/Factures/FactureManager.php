<?php

namespace App\Livewire\Factures;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\Sale;

class FactureManager extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $filterMonth = '';
    public string $filterStatus = '';

    public function mount(): void
    {
        $this->filterMonth = now()->format('Y-m');
    }

    public function updatingSearch(): void { $this->resetPage(); }

    public function render()
    {
        $invoices = Invoice::with(['sale.customer', 'sale.user'])
            ->when($this->search, fn($q) =>
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('sale.customer', fn($q2) =>
                      $q2->where('name', 'like', '%' . $this->search . '%')
                  )
            )
            ->when($this->filterMonth, fn($q) =>
                $q->whereYear('issued_at', substr($this->filterMonth, 0, 4))
                  ->whereMonth('issued_at', substr($this->filterMonth, 5, 2))
            )
            ->latest('issued_at')
            ->paginate(15);

        $stats = [
            'total'   => Invoice::when($this->filterMonth, fn($q) =>
                $q->whereYear('issued_at', substr($this->filterMonth, 0, 4))
                  ->whereMonth('issued_at', substr($this->filterMonth, 5, 2))
            )->sum('total_ttc'),
            'count'   => Invoice::when($this->filterMonth, fn($q) =>
                $q->whereYear('issued_at', substr($this->filterMonth, 0, 4))
                  ->whereMonth('issued_at', substr($this->filterMonth, 5, 2))
            )->count(),
        ];

        return view('livewire.factures.facture-manager',
            compact('invoices', 'stats'));
    }
}
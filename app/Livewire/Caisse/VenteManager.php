<?php

namespace App\Livewire\Caisse;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sale;
use Carbon\Carbon;

class VenteManager extends Component
{
    use WithPagination;

    public string $search      = '';
    public string $filterDate  = '';
    public string $filterMonth = '';
    public string $filterSource = '';
    public string $filterPayment = '';
    public string $dateFrom    = '';
    public string $dateTo      = '';

    // Détail vente
    public bool $showDetailModal = false;
    public ?int $viewingSaleId   = null;

    public function mount(): void
    {
        $this->filterMonth = now()->format('Y-m');
    }

    public function updatingSearch(): void { $this->resetPage(); }

    public function viewSale(int $id): void
    {
        $this->viewingSaleId   = $id;
        $this->showDetailModal = true;
    }

    public function render()
    {
        $sales = Sale::with(['customer', 'user', 'items', 'invoice'])
            ->when($this->search, fn($q) =>
                $q->where('reference', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', fn($q2) =>
                      $q2->where('name', 'like', '%' . $this->search . '%')
                  )
            )
            ->when($this->filterSource, fn($q) =>
                $q->where('source', $this->filterSource)
            )
            ->when($this->filterPayment, fn($q) =>
                $q->where('payment_method', $this->filterPayment)
            )
            ->when($this->filterMonth && !$this->dateFrom, fn($q) =>
                $q->whereYear('sold_at', substr($this->filterMonth, 0, 4))
                  ->whereMonth('sold_at', substr($this->filterMonth, 5, 2))
            )
            ->when($this->dateFrom, fn($q) =>
                $q->whereDate('sold_at', '>=', $this->dateFrom)
            )
            ->when($this->dateTo, fn($q) =>
                $q->whereDate('sold_at', '<=', $this->dateTo)
            )
            ->latest('sold_at')
            ->paginate(15);

        // Stats période
        $statsQuery = Sale::where('status', 'completed')
            ->when($this->filterMonth && !$this->dateFrom, fn($q) =>
                $q->whereYear('sold_at', substr($this->filterMonth, 0, 4))
                  ->whereMonth('sold_at', substr($this->filterMonth, 5, 2))
            );

        $stats = [
            'count'    => $statsQuery->count(),
            'total'    => $statsQuery->sum('total_ttc'),
            'cash'     => (clone $statsQuery)->where('payment_method', 'cash')->sum('total_ttc'),
            'wave'     => (clone $statsQuery)->where('payment_method', 'wave')->sum('total_ttc'),
            'card'     => (clone $statsQuery)->where('payment_method', 'card')->sum('total_ttc'),
            'om'       => (clone $statsQuery)->where('payment_method', 'om')->sum('total_ttc'),
        ];

        $viewingSale = $this->viewingSaleId
            ? Sale::with(['customer', 'user', 'items.product', 'invoice'])
                  ->find($this->viewingSaleId)
            : null;

        return view('livewire.caisse.vente-manager', compact('sales', 'stats', 'viewingSale'));
    }
}
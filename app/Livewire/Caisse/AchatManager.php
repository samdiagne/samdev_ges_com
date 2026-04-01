<?php

namespace App\Livewire\Caisse;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Purchase;

class AchatManager extends Component
{
    use WithPagination;

    public string $search         = '';
    public string $filterMonth    = '';
    public string $filterStatus   = '';
    public string $filterSupplier = '';

    public bool $showDetailModal  = false;
    public ?int $viewingPurchaseId = null;

    public bool $showDeleteModal  = false;
    public ?int $deletingId       = null;

    public function mount(): void
    {
        $this->filterMonth = now()->format('Y-m');
    }

    public function updatingSearch(): void { $this->resetPage(); }

    public function viewPurchase(int $id): void
    {
        $this->viewingPurchaseId = $id;
        $this->showDetailModal   = true;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $purchase = Purchase::findOrFail($this->deletingId);
        $purchase->delete();
        session()->flash('success', 'Achat supprimé avec succès !');
        $this->showDeleteModal = false;
        $this->deletingId      = null;
        $this->resetPage();
    }

    public function render()
    {
        $purchases = Purchase::with(['supplier', 'user', 'items'])
            ->when($this->search, fn($q) =>
                $q->where('reference', 'like', '%' . $this->search . '%')
                  ->orWhereHas('supplier', fn($q2) =>
                      $q2->where('name', 'like', '%' . $this->search . '%')
                  )
            )
            ->when($this->filterStatus, fn($q) =>
                $q->where('status', $this->filterStatus)
            )
            ->when($this->filterSupplier, fn($q) =>
                $q->where('supplier_id', $this->filterSupplier)
            )
            ->when($this->filterMonth, fn($q) =>
                $q->whereYear('purchase_date', substr($this->filterMonth, 0, 4))
                  ->whereMonth('purchase_date', substr($this->filterMonth, 5, 2))
            )
            ->latest('purchase_date')
            ->paginate(15);

        $statsQuery = Purchase::when($this->filterMonth, fn($q) =>
            $q->whereYear('purchase_date', substr($this->filterMonth, 0, 4))
              ->whereMonth('purchase_date', substr($this->filterMonth, 5, 2))
        );

        $stats = [
            'count'    => $statsQuery->count(),
            'total'    => $statsQuery->sum('total_amount'),
            'paid'     => $statsQuery->sum('amount_paid'),
            'due'      => $statsQuery->sum('amount_due'),
        ];

        $suppliers = \App\Models\Supplier::where('is_active', true)
            ->orderBy('name')->get(['id', 'name']);

        $viewingPurchase = $this->viewingPurchaseId
            ? Purchase::with(['supplier', 'user', 'items.product'])
                      ->find($this->viewingPurchaseId)
            : null;

        return view('livewire.caisse.achat-manager',
            compact('purchases', 'stats', 'suppliers', 'viewingPurchase'));
    }
}
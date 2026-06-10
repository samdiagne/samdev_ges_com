<?php

namespace App\Livewire\Devis;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Quote;

class DevisManager extends Component
{
    use WithPagination;

    public string $search        = '';
    public string $filterStatus  = '';
    public string $filterMonth   = '';

    public bool  $showDeleteModal    = false;
    public ?int  $deletingId         = null;
    public bool  $showConvertModal   = false;
    public ?int  $convertingId       = null;
    public string $paymentMethod     = 'cash';
    public float  $amountPaid        = 0;

    public function mount(): void
    {
        $this->filterMonth = now()->format('Y-m');
    }

    public function updatingSearch(): void { $this->resetPage(); }

    public function markAsSent(int $id): void
    {
        Quote::where('id', $id)->update(['status' => 'sent', 'envoye_whatsapp' => true]);
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        Quote::findOrFail($this->deletingId)->delete();
        session()->flash('success', 'Devis supprimé !');
        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    public function openConvertModal(int $id): void
    {
        $quote               = Quote::findOrFail($id);
        $this->convertingId  = $id;
        $this->amountPaid    = (float) $quote->total;
        $this->paymentMethod = 'cash';
        $this->showConvertModal = true;
    }

    public function convertToSale(\App\Services\QuoteService $quoteService): void
    {
        $quote = Quote::with('items')->findOrFail($this->convertingId);

        if ($this->amountPaid < $quote->total) {
            $this->addError('amountPaid', 'Montant insuffisant !');
            return;
        }

        $sale = $quoteService->convertToSale($quote, [
            'method'      => $this->paymentMethod,
            'amount_paid' => $this->amountPaid,
        ]);

        session()->flash('success',
            'Devis converti en vente ' . $sale->reference . ' avec succès !');
        $this->showConvertModal = false;
        $this->convertingId     = null;
    }

    public function render()
    {
        $quotes = Quote::with(['client', 'user'])
            ->when($this->search, fn($q) =>
                $q->where('reference', 'like', '%' . $this->search . '%')
                  ->orWhere('client_nom', 'like', '%' . $this->search . '%')
                  ->orWhereHas('client', fn($q2) =>
                      $q2->where('nom', 'like', '%' . $this->search . '%')
                  )
            )
            ->when($this->filterStatus, fn($q) =>
                $q->where('status', $this->filterStatus)
            )
            ->when($this->filterMonth, fn($q) =>
                $q->whereYear('date_devis', substr($this->filterMonth, 0, 4))
                  ->whereMonth('date_devis', substr($this->filterMonth, 5, 2))
            )
            ->latest('date_devis')
            ->paginate(15);

        $stats = [
            'total'    => Quote::whereMonth('date_devis', now()->month)->count(),
            'draft'    => Quote::where('status', 'draft')->count(),
            'sent'     => Quote::where('status', 'sent')->count(),
            'accepted' => Quote::where('status', 'accepted')->count(),
            'ca'       => Quote::where('status', 'accepted')
                ->whereMonth('date_devis', now()->month)->sum('total'),
        ];

        return view('livewire.devis.devis-manager', compact('quotes', 'stats'));
    }
}

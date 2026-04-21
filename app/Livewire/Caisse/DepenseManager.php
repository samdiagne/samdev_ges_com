<?php

namespace App\Livewire\Caisse;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\Auth;

class DepenseManager extends Component
{
    use WithPagination, WithFileUploads;

    public string $search          = '';
    public string $filterMonth     = '';
    public string $filterCategory  = '';

    // Form
    public bool   $showModal       = false;
    public bool   $isEditing       = false;
    public ?int   $editingId       = null;

    // Champs
    public int    $expense_category_id = 0;
    public string $description         = '';
    public string $amount              = '';
    public string $expense_date        = '';
    public string $payment_method      = 'cash';
    public string $notes               = '';
    public        $receipt             = null;
    public ?string $currentReceipt     = null;

    // Suppression
    public bool $showDeleteModal = false;
    public ?int $deletingId      = null;

    // Verrouillage montant
    public bool $amountLocked = false;

    protected function rules(): array
    {
        return [
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description'         => 'required|string|min:2|max:255',
            'amount' => 'required|numeric|min:1|max:999999999',
            'expense_date'        => 'required|date',
            'payment_method'      => 'required|string',
            'notes'               => 'nullable|string|max:500',
            'receipt'             => 'nullable|image|max:2048',
        ];
    }

    protected $messages = [
        'expense_category_id.required' => 'Veuillez sélectionner une catégorie.',
        'description.required'         => 'La description est obligatoire.',
        'amount.required'              => 'Le montant est obligatoire.',
        'amount.min'                   => 'Le montant doit être supérieur à 0.',
        'expense_date.required'        => 'La date est obligatoire.',
    ];

    public function mount(): void
    {
        $this->filterMonth  = now()->format('Y-m');
        $this->expense_date = now()->format('Y-m-d');
    }

    public function updatingSearch(): void { $this->resetPage(); }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isEditing    = false;
        $this->expense_date = now()->format('Y-m-d');
        $this->showModal    = true;
    }

    public function openEditModal(int $id): void
    {
        $expense = Expense::findOrFail($id);
        $this->resetForm();
        $this->isEditing            = true;
        $this->editingId            = $id;
        $this->amountLocked         = !$expense->isDeletable();
        $this->expense_category_id  = $expense->expense_category_id;
        $this->description          = $expense->description;
        $this->amount               = (float) $expense->amount;
        $this->expense_date         = $expense->expense_date->format('Y-m-d');
        $this->payment_method       = $expense->payment_method;
        $this->notes                = $expense->notes ?? '';
        $this->currentReceipt       = $expense->receipt;
        $this->showModal            = true;
    }

    public function save(): void
    {
        $this->validate();

        $receiptPath = $this->currentReceipt;
        if ($this->receipt) {
            $receiptPath = $this->receipt->store('receipts', 'public');
        }

        $data = [
            'expense_category_id' => $this->expense_category_id,
            'user_id'             => Auth::id(),
            'description'         => $this->description,
            'amount'              => (float) $this->amount,
            'expense_date'        => $this->expense_date,
            'payment_method'      => $this->payment_method,
            'notes'               => $this->notes ?: null,
            'receipt'             => $receiptPath,
        ];

        if ($this->isEditing) {
            $expense = Expense::findOrFail($this->editingId);
            if (!$expense->isDeletable()) {
                unset($data['amount']);
            }
            $expense->update($data);
            session()->flash('success', 'Dépense modifiée avec succès !');
        } else {
            Expense::create($data);
            session()->flash('success', 'Dépense enregistrée avec succès !');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $expense = Expense::findOrFail($id);

        if (!$expense->isDeletable()) {
            session()->flash('error', 'Cette dépense ne peut plus être supprimée (plus de 24h).');
            return;
        }

        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $expense = Expense::findOrFail($this->deletingId);

        if (!$expense->isDeletable()) {
            session()->flash('error', 'Cette dépense ne peut plus être supprimée (plus de 24h).');
            $this->showDeleteModal = false;
            $this->deletingId      = null;
            return;
        }

        $expense->delete();
        session()->flash('success', 'Dépense supprimée avec succès !');
        $this->showDeleteModal = false;
        $this->deletingId      = null;
        $this->resetPage();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->expense_category_id = 0;
        $this->description         = '';
        $this->amount              = '';
        $this->expense_date        = now()->format('Y-m-d');
        $this->payment_method      = 'cash';
        $this->notes               = '';
        $this->receipt             = null;
        $this->currentReceipt      = null;
        $this->editingId           = null;
        $this->amountLocked        = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        $expenses = Expense::with(['category', 'user'])
            ->when($this->search, fn($q) =>
                $q->where('description', 'like', '%' . $this->search . '%')
            )
            ->when($this->filterCategory, fn($q) =>
                $q->where('expense_category_id', $this->filterCategory)
            )
            ->when($this->filterMonth, fn($q) =>
                $q->whereYear('expense_date', substr($this->filterMonth, 0, 4))
                  ->whereMonth('expense_date', substr($this->filterMonth, 5, 2))
            )
            ->latest('expense_date')
            ->paginate(15);

        // Stats période
        $statsQuery = Expense::when($this->filterMonth, fn($q) =>
            $q->whereYear('expense_date', substr($this->filterMonth, 0, 4))
              ->whereMonth('expense_date', substr($this->filterMonth, 5, 2))
        );

        $stats = [
            'total'      => $statsQuery->sum('amount'),
            'count'      => $statsQuery->count(),
            'by_category'=> ExpenseCategory::withSum(
                ['expenses' => fn($q) =>
                    $q->when($this->filterMonth, fn($q2) =>
                        $q2->whereYear('expense_date', substr($this->filterMonth, 0, 4))
                           ->whereMonth('expense_date', substr($this->filterMonth, 5, 2))
                    )
                ],
                'amount'
            )->having('expenses_sum_amount', '>', 0)->get(),
        ];

        $expenseCategories = ExpenseCategory::orderBy('name')->get();

        return view('livewire.caisse.depense-manager',
            compact('expenses', 'stats', 'expenseCategories'));
    }
}
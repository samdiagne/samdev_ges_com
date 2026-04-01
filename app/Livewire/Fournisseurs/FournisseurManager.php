<?php

namespace App\Livewire\Fournisseurs;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;

class FournisseurManager extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $filterStatus = '';

    // Form
    public bool   $showModal  = false;
    public bool   $isEditing  = false;
    public ?int   $editingId  = null;

    // Champs
    public string $name           = '';
    public string $contact_person = '';
    public string $phone          = '';
    public string $whatsapp       = '';
    public string $email          = '';
    public string $address        = '';
    public string $ninea          = '';
    public string $notes          = '';
    public bool   $is_active      = true;

    // Suppression
    public bool $showDeleteModal = false;
    public ?int $deletingId      = null;

    protected function rules(): array
    {
        return [
            'name'           => 'required|string|min:2|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'whatsapp'       => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:500',
            'ninea'          => 'nullable|string|max:50',
            'notes'          => 'nullable|string|max:1000',
            'is_active'      => 'boolean',
        ];
    }

    protected $messages = [
        'name.required' => 'Le nom du fournisseur est obligatoire.',
        'email.email'   => 'Adresse email invalide.',
    ];

    public function updatingSearch(): void { $this->resetPage(); }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $supplier = Supplier::findOrFail($id);
        $this->resetForm();
        $this->isEditing      = true;
        $this->editingId      = $id;
        $this->name           = $supplier->name;
        $this->contact_person = $supplier->contact_person ?? '';
        $this->phone          = $supplier->phone ?? '';
        $this->whatsapp       = $supplier->whatsapp ?? '';
        $this->email          = $supplier->email ?? '';
        $this->address        = $supplier->address ?? '';
        $this->ninea          = $supplier->ninea ?? '';
        $this->notes          = $supplier->notes ?? '';
        $this->is_active      = $supplier->is_active;
        $this->showModal      = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'           => $this->name,
            'contact_person' => $this->contact_person ?: null,
            'phone'          => $this->phone ?: null,
            'whatsapp'       => $this->whatsapp ?: null,
            'email'          => $this->email ?: null,
            'address'        => $this->address ?: null,
            'ninea'          => $this->ninea ?: null,
            'notes'          => $this->notes ?: null,
            'is_active'      => $this->is_active,
        ];

        if ($this->isEditing) {
            Supplier::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Fournisseur modifié avec succès !');
        } else {
            Supplier::create($data);
            session()->flash('success', 'Fournisseur créé avec succès !');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $supplier = Supplier::findOrFail($this->deletingId);

        if ($supplier->purchases()->count() > 0) {
            session()->flash('error',
                'Impossible de supprimer : ce fournisseur a des achats associés.');
            $this->showDeleteModal = false;
            return;
        }

        $supplier->delete();
        session()->flash('success', 'Fournisseur supprimé avec succès !');
        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    public function toggleStatus(int $id): void
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update(['is_active' => !$supplier->is_active]);
        session()->flash('success', 'Statut mis à jour !');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->name           = '';
        $this->contact_person = '';
        $this->phone          = '';
        $this->whatsapp       = '';
        $this->email          = '';
        $this->address        = '';
        $this->ninea          = '';
        $this->notes          = '';
        $this->is_active      = true;
        $this->editingId      = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        $suppliers = Supplier::withCount('purchases')
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
            )
            ->when($this->filterStatus !== '', fn($q) =>
                $q->where('is_active', $this->filterStatus === '1')
            )
            ->latest()
            ->paginate(10);

        return view('livewire.fournisseurs.fournisseur-manager',
            compact('suppliers'));
    }
}
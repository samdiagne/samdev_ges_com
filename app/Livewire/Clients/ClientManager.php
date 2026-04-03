<?php

namespace App\Livewire\Clients;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;

class ClientManager extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $filterType   = '';
    public string $filterStatus = '';

    // Form
    public bool  $showModal  = false;
    public bool  $isEditing  = false;
    public ?int  $editingId  = null;

    // Champs
    public string $name         = '';
    public string $phone        = '';
    public string $whatsapp     = '';
    public string $email        = '';
    public string $address      = '';
    public string $type         = 'individual';
    public string $company_name = '';
    public string $ninea        = '';
    public string $notes        = '';

    // Détail
    public bool $showDetailModal = false;
    public ?int $viewingId       = null;

    // Suppression
    public bool $showDeleteModal = false;
    public ?int $deletingId      = null;

    protected function rules(): array
    {
        return [
            'name'         => 'required|string|min:2|max:255',
            'phone'        => 'nullable|string|max:20|unique:customers,phone,' . ($this->editingId ?? 'NULL'),
            'whatsapp'     => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string|max:500',
            'type'         => 'required|in:individual,company',
            'company_name' => 'nullable|string|max:255',
            'ninea'        => 'nullable|string|max:50',
            'notes'        => 'nullable|string|max:1000',
        ];
    }

    protected $messages = [
        'name.required' => 'Le nom du client est obligatoire.',
        'email.email'   => 'Adresse email invalide.',
        'phone.unique'  => 'Ce numéro de téléphone est déjà utilisé par un autre client.',
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
        $customer = Customer::findOrFail($id);
        $this->resetForm();
        $this->isEditing    = true;
        $this->editingId    = $id;
        $this->name         = $customer->name;
        $this->phone        = $customer->phone ?? '';
        $this->whatsapp     = $customer->whatsapp ?? '';
        $this->email        = $customer->email ?? '';
        $this->address      = $customer->address ?? '';
        $this->type         = $customer->type;
        $this->company_name = $customer->company_name ?? '';
        $this->ninea        = $customer->ninea ?? '';
        $this->notes        = $customer->notes ?? '';
        $this->showModal    = true;
    }

    public function viewCustomer(int $id): void
    {
        $this->viewingId       = $id;
        $this->showDetailModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'         => $this->name,
            'phone'        => $this->phone ?: null,
            'whatsapp'     => $this->whatsapp ?: null,
            'email'        => $this->email ?: null,
            'address'      => $this->address ?: null,
            'type'         => $this->type,
            'company_name' => $this->company_name ?: null,
            'ninea'        => $this->ninea ?: null,
            'notes'        => $this->notes ?: null,
        ];

        if ($this->isEditing) {
            Customer::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Client modifié avec succès !');
        } else {
            Customer::create($data);
            session()->flash('success', 'Client créé avec succès !');
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
        $customer = Customer::findOrFail($this->deletingId);

        if ($customer->sales()->count() > 0) {
            session()->flash('error',
                'Impossible de supprimer : ce client a des ventes associées.');
            $this->showDeleteModal = false;
            return;
        }

        $customer->delete();
        session()->flash('success', 'Client supprimé avec succès !');
        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->name         = '';
        $this->phone        = '';
        $this->whatsapp     = '';
        $this->email        = '';
        $this->address      = '';
        $this->type         = 'individual';
        $this->company_name = '';
        $this->ninea        = '';
        $this->notes        = '';
        $this->editingId    = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        $customers = Customer::withCount('sales')
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
            )
            ->when($this->filterType, fn($q) =>
                $q->where('type', $this->filterType)
            )
            ->latest()
            ->paginate(15);

        $viewingCustomer = $this->viewingId
            ? Customer::withCount(['sales', 'orders'])
                      ->with([
                          'sales' => fn($q) => $q->with('invoice')->latest('sold_at')->limit(5),
                          'orders' => fn($q) => $q->latest()->limit(5),
                      ])
                      ->find($this->viewingId)
            : null;

        $stats = [
            'total'      => Customer::count(),
            'individual' => Customer::where('type', 'individual')->count(),
            'company'    => Customer::where('type', 'company')->count(),
        ];

        return view('livewire.clients.client-manager',
            compact('customers', 'viewingCustomer', 'stats'));
    }
}
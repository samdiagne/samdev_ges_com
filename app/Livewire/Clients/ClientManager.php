<?php

namespace App\Livewire\Clients;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Client;

class ClientManager extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $filtreStatut = '';

    public string $sortField     = 'created_at';
    public string $sortDirection = 'desc';
    public string $tri           = 'created_at:desc';

    public bool   $afficherModal = false;
    public bool   $isEditing     = false;
    public ?int   $editingId     = null;

    public string $nom       = '';
    public string $telephone = '';
    public string $email     = '';
    public string $adresse   = '';
    public string $ville     = '';
    public bool   $est_actif = true;

    public bool   $afficherModalDetail      = false;
    public ?int   $voirId                   = null;
    public bool   $afficherModalSuppression = false;
    public ?int   $suppressionId            = null;

    protected function rules(): array
    {
        return [
            'nom'       => 'required|string|min:2|max:255',
            'telephone' => 'nullable|string|max:20|unique:clients,telephone,' . ($this->editingId ?? 'NULL'),
            'email'     => 'nullable|email|max:255',
            'adresse'   => 'nullable|string|max:500',
            'ville'     => 'nullable|string|max:100',
        ];
    }

    protected $messages = [
        'nom.required'      => 'Le nom du client est obligatoire.',
        'email.email'       => 'Adresse email invalide.',
        'telephone.unique'  => 'Ce numéro est déjà utilisé par un autre client.',
    ];

    public function updatingSearch(): void { $this->resetPage(); }

    public function sortBy(string $field): void
    {
        if (! in_array($field, ['nom', 'ventes_count', 'total_achats', 'created_at'])) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'desc';
        }

        $this->tri = $this->sortField . ':' . $this->sortDirection;
        $this->resetPage();
    }

    public function updatedTri(string $value): void
    {
        [$field, $direction] = array_pad(explode(':', $value), 2, 'desc');

        if (in_array($field, ['nom', 'ventes_count', 'total_achats', 'created_at'])
            && in_array($direction, ['asc', 'desc'])) {
            $this->sortField     = $field;
            $this->sortDirection = $direction;
            $this->resetPage();
        }
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isEditing     = false;
        $this->afficherModal = true;
    }

    public function openEditModal(int $id): void
    {
        $client = Client::findOrFail($id);
        $this->resetForm();
        $this->isEditing  = true;
        $this->editingId  = $id;
        $this->nom        = $client->nom;
        $this->telephone  = $client->telephone ?? '';
        $this->email      = $client->email ?? '';
        $this->adresse    = $client->adresse ?? '';
        $this->ville      = $client->ville ?? '';
        $this->est_actif  = $client->est_actif;
        $this->afficherModal = true;
    }

    public function view(int $id): void
    {
        $this->voirId                = $id;
        $this->afficherModalDetail   = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'nom'       => $this->nom,
            'telephone' => $this->telephone ?: null,
            'email'     => $this->email ?: null,
            'adresse'   => $this->adresse ?: null,
            'ville'     => $this->ville ?: null,
            'est_actif' => $this->est_actif,
        ];

        if ($this->isEditing) {
            Client::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Client modifié avec succès !');
        } else {
            Client::create($data);
            session()->flash('success', 'Client créé avec succès !');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->suppressionId            = $id;
        $this->afficherModalSuppression = true;
    }

    public function delete(): void
    {
        $client = Client::findOrFail($this->suppressionId);
        if ($client->ventes()->count() > 0) {
            session()->flash('error', 'Impossible : ce client a des ventes associées.');
            $this->afficherModalSuppression = false;
            return;
        }
        $client->delete();
        session()->flash('success', 'Client supprimé avec succès !');
        $this->afficherModalSuppression = false;
        $this->suppressionId            = null;
    }

    public function closeModal(): void
    {
        $this->afficherModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->nom        = '';
        $this->telephone  = '';
        $this->email      = '';
        $this->adresse    = '';
        $this->ville      = '';
        $this->est_actif  = true;
        $this->editingId  = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        $clients = Client::withCount('ventes')
            ->withSum(['ventes as total_achats' => fn($q) => $q->where('statut', 'completee')], 'total')
            ->when($this->search, fn($q) =>
                $q->where(fn($sub) =>
                    $sub->where('nom', 'like', '%' . $this->search . '%')
                        ->orWhere('telephone', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                )
            )
            ->when($this->sortField === 'total_achats',
                fn($q) => $q->orderByRaw('COALESCE(total_achats, 0) ' . $this->sortDirection),
                fn($q) => $q->orderBy($this->sortField, $this->sortDirection)
            )
            ->paginate(15);

        $voirClient = $this->voirId
            ? Client::withCount(['ventes', 'commandes'])
                ->with(['ventes' => fn($q) => $q->with('facture')->latest('vendu_le')->limit(5)])
                ->find($this->voirId)
            : null;

        $stats = [
            'total'  => Client::count(),
            'actifs' => Client::where('est_actif', true)->count(),
        ];

        return view('livewire.clients.client-manager',
            compact('clients', 'voirClient', 'stats'));
    }
}

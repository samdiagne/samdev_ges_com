<?php

namespace App\Livewire\Utilisateurs;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UtilisateurManager extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $filtreRole   = '';
    public string $filtreStatut = '';

    public bool   $afficherModal = false;
    public bool   $isEditing     = false;
    public ?int   $editingId     = null;

    public string $name      = '';
    public string $email     = '';
    public string $password  = '';
    public string $role      = 'gestionnaire';
    public bool   $is_active = true;

    public bool   $afficherModalSuppression = false;
    public ?int   $suppressionId            = null;

    protected function rules(): array
    {
        return [
            'name'     => 'required|string|min:2|max:255',
            'email'    => 'required|email|unique:users,email,' . ($this->editingId ?? 'NULL'),
            'password' => $this->isEditing ? 'nullable|min:8' : 'required|min:8',
            'role'     => 'required|in:admin,gestionnaire',
            'is_active'=> 'boolean',
        ];
    }

    protected $messages = [
        'name.required'     => 'Le nom est obligatoire.',
        'email.required'    => 'L\'email est obligatoire.',
        'email.unique'      => 'Cet email est déjà utilisé.',
        'password.required' => 'Le mot de passe est obligatoire.',
        'password.min'      => 'Le mot de passe doit faire au moins 8 caractères.',
        'role.required'     => 'Veuillez choisir un rôle.',
    ];

    public function updatingSearch(): void { $this->resetPage(); }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isEditing     = false;
        $this->afficherModal = true;
    }

    public function openEditModal(int $id): void
    {
        $user = User::findOrFail($id);
        $this->resetForm();
        $this->isEditing  = true;
        $this->editingId  = $id;
        $this->name       = $user->name;
        $this->email      = $user->email;
        $this->role       = $user->roles->first()?->name ?? 'gestionnaire';
        $this->is_active  = $user->is_active;
        $this->afficherModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'      => $this->name,
            'email'     => $this->email,
            'is_active' => $this->is_active,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEditing) {
            $user = User::findOrFail($this->editingId);

            if ($user->id === auth()->id() && $this->role !== 'admin') {
                $this->addError('role', 'Vous ne pouvez pas changer votre propre rôle.');
                return;
            }

            $user->update($data);
            $user->syncRoles([$this->role]);
            session()->flash('success', 'Utilisateur modifié avec succès !');
        } else {
            $user = User::create($data);
            $user->assignRole($this->role);
            session()->flash('success', 'Utilisateur créé avec succès !');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function toggleStatus(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Vous ne pouvez pas désactiver votre propre compte.');
            return;
        }
        $user->update(['is_active' => ! $user->is_active]);
        session()->flash('success', 'Statut mis à jour !');
    }

    public function confirmDelete(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return;
        }
        $this->suppressionId            = $id;
        $this->afficherModalSuppression = true;
    }

    public function delete(): void
    {
        $user = User::findOrFail($this->suppressionId);
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Action non autorisée.');
            $this->afficherModalSuppression = false;
            return;
        }
        $user->delete();
        session()->flash('success', 'Utilisateur supprimé avec succès !');
        $this->afficherModalSuppression = false;
        $this->suppressionId            = null;
        $this->resetPage();
    }

    public function closeModal(): void
    {
        $this->afficherModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->name       = '';
        $this->email      = '';
        $this->password   = '';
        $this->role       = 'gestionnaire';
        $this->is_active  = true;
        $this->editingId  = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        $users = User::with('roles')
            ->withCount('ventes')
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
            )
            ->when($this->filtreRole, fn($q) =>
                $q->role($this->filtreRole)
            )
            ->when($this->filtreStatut !== '', fn($q) =>
                $q->where('is_active', $this->filtreStatut === '1')
            )
            ->latest()
            ->paginate(15);

        $stats = [
            'total'         => User::count(),
            'admins'        => User::role('admin')->count(),
            'gestionnaires' => User::role('gestionnaire')->count(),
            'inactifs'      => User::where('is_active', false)->count(),
        ];

        return view('livewire.utilisateurs.utilisateur-manager',
            compact('users', 'stats'));
    }
}

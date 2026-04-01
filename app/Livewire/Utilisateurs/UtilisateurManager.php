<?php

namespace App\Livewire\Utilisateurs;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UtilisateurManager extends Component
{
    use WithPagination;

    public bool   $showModal  = false;
    public bool   $isEditing  = false;
    public ?int   $editingId  = null;

    public string $name     = '';
    public string $email    = '';
    public string $phone    = '';
    public string $password = '';
    public string $role     = 'gestionnaire';
    public bool   $is_active = true;

    public bool $showDeleteModal = false;
    public ?int $deletingId      = null;

    protected function rules(): array
    {
        return [
            'name'     => 'required|string|min:2|max:255',
            'email'    => 'required|email|unique:users,email,' . ($this->editingId ?? 'NULL'),
            'phone'    => 'nullable|string|max:20',
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
    ];

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $user = User::findOrFail($id);
        $this->resetForm();
        $this->isEditing = true;
        $this->editingId = $id;
        $this->name      = $user->name;
        $this->email     = $user->email;
        $this->phone     = $user->phone ?? '';
        $this->role      = $user->getRoleNames()->first() ?? 'gestionnaire';
        $this->is_active = $user->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'      => $this->name,
            'email'     => $this->email,
            'phone'     => $this->phone ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEditing) {
            $user = User::findOrFail($this->editingId);
            $user->update($data);
            $user->syncRoles([$this->role]);
            session()->flash('success', 'Utilisateur modifié !');
        } else {
            $user = User::create($data);
            $user->assignRole($this->role);
            session()->flash('success', 'Utilisateur créé !');
        }

        $this->closeModal();
    }

    public function toggleStatus(int $id): void
    {
        $user = User::findOrFail($id);

        // Empêcher de se désactiver soi-même
        if ($user->id === auth()->id()) {
            session()->flash('error', 'Vous ne pouvez pas désactiver votre propre compte.');
            return;
        }

        $user->update(['is_active' => !$user->is_active]);
        session()->flash('success', 'Statut mis à jour !');
    }

    public function confirmDelete(int $id): void
    {
        // Empêcher de se supprimer soi-même
        if ($id === auth()->id()) {
            session()->flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return;
        }
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        User::findOrFail($this->deletingId)->delete();
        session()->flash('success', 'Utilisateur supprimé !');
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
        $this->name      = '';
        $this->email     = '';
        $this->phone     = '';
        $this->password  = '';
        $this->role      = 'gestionnaire';
        $this->is_active = true;
        $this->editingId = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        $users = User::with('roles')
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['admin', 'gestionnaire']))
            ->latest()
            ->paginate(15);

        return view('livewire.utilisateurs.utilisateur-manager',
            compact('users'));
    }
}
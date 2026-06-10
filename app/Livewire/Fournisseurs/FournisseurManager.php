<?php

namespace App\Livewire\Fournisseurs;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Fournisseur;

class FournisseurManager extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $filtreStatut = '';

    public bool   $afficherModal = false;
    public bool   $isEditing     = false;
    public ?int   $editingId     = null;

    public string $nom       = '';
    public string $telephone = '';
    public string $email     = '';
    public string $adresse   = '';
    public string $notes     = '';
    public bool   $est_actif = true;

    public bool   $afficherModalSuppression = false;
    public ?int   $suppressionId            = null;

    protected function rules(): array
    {
        return [
            'nom'       => 'required|string|min:2|max:255',
            'telephone' => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'adresse'   => 'nullable|string|max:500',
            'notes'     => 'nullable|string|max:1000',
            'est_actif' => 'boolean',
        ];
    }

    protected $messages = [
        'nom.required' => 'Le nom du fournisseur est obligatoire.',
        'email.email'  => 'Adresse email invalide.',
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
        $fournisseur = Fournisseur::findOrFail($id);
        $this->resetForm();
        $this->isEditing  = true;
        $this->editingId  = $id;
        $this->nom        = $fournisseur->nom;
        $this->telephone  = $fournisseur->telephone ?? '';
        $this->email      = $fournisseur->email ?? '';
        $this->adresse    = $fournisseur->adresse ?? '';
        $this->notes      = $fournisseur->notes ?? '';
        $this->est_actif  = $fournisseur->est_actif;
        $this->afficherModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'nom'       => $this->nom,
            'telephone' => $this->telephone ?: null,
            'email'     => $this->email ?: null,
            'adresse'   => $this->adresse ?: null,
            'notes'     => $this->notes ?: null,
            'est_actif' => $this->est_actif,
        ];

        if ($this->isEditing) {
            Fournisseur::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Fournisseur modifié avec succès !');
        } else {
            Fournisseur::create($data);
            session()->flash('success', 'Fournisseur créé avec succès !');
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
        $fournisseur = Fournisseur::findOrFail($this->suppressionId);
        if ($fournisseur->achats()->count() > 0) {
            session()->flash('error', 'Impossible : ce fournisseur a des achats associés.');
            $this->afficherModalSuppression = false;
            return;
        }
        $fournisseur->delete();
        session()->flash('success', 'Fournisseur supprimé avec succès !');
        $this->afficherModalSuppression = false;
        $this->suppressionId            = null;
    }

    public function toggleStatus(int $id): void
    {
        $fournisseur = Fournisseur::findOrFail($id);
        $fournisseur->update(['est_actif' => ! $fournisseur->est_actif]);
        session()->flash('success', 'Statut mis à jour !');
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
        $this->notes      = '';
        $this->est_actif  = true;
        $this->editingId  = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        $fournisseurs = Fournisseur::withCount('achats')
            ->when($this->search, fn($q) =>
                $q->where('nom', 'like', '%' . $this->search . '%')
                  ->orWhere('telephone', 'like', '%' . $this->search . '%')
            )
            ->when($this->filtreStatut !== '', fn($q) =>
                $q->where('est_actif', $this->filtreStatut === '1')
            )
            ->latest()
            ->paginate(10);

        return view('livewire.fournisseurs.fournisseur-manager', compact('fournisseurs'));
    }
}

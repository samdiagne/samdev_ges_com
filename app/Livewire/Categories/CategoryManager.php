<?php

namespace App\Livewire\Categories;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Categorie;
use Illuminate\Support\Str;

class CategoryManager extends Component
{
    use WithPagination, WithFileUploads;

    public string $search        = '';
    public string $filtreStatut  = '';

    public bool   $afficherModal = false;
    public bool   $isEditing     = false;
    public ?int   $editingId     = null;

    public string  $nom          = '';
    public string  $description  = '';
    public bool    $est_active   = true;
    public         $image        = null;
    public ?string $imageActuelle = null;

    public bool    $afficherModalSuppression = false;
    public ?int    $suppressionId            = null;

    protected function rules(): array
    {
        return [
            'nom'         => 'required|string|min:2|max:100',
            'description' => 'nullable|string|max:500',
            'est_active'  => 'boolean',
            'image'       => 'nullable|image|max:2048',
        ];
    }

    protected $messages = [
        'nom.required' => 'Le nom de la catégorie est obligatoire.',
        'nom.min'      => 'Le nom doit contenir au moins 2 caractères.',
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
        $categorie = Categorie::findOrFail($id);
        $this->resetForm();
        $this->isEditing      = true;
        $this->editingId      = $id;
        $this->nom            = $categorie->nom;
        $this->description    = $categorie->description ?? '';
        $this->est_active     = $categorie->est_active;
        $this->imageActuelle  = $categorie->image;
        $this->afficherModal  = true;
    }

    public function save(): void
    {
        $this->validate();

        $cheminImage = $this->imageActuelle;
        if ($this->image) {
            $cheminImage = $this->image->store('categories', 'public');
        }

        if ($this->isEditing) {
            Categorie::findOrFail($this->editingId)->update([
                'nom'         => $this->nom,
                'description' => $this->description,
                'est_active'  => $this->est_active,
                'image'       => $cheminImage,
            ]);
            session()->flash('success', 'Catégorie modifiée avec succès !');
        } else {
            $slug     = Str::slug($this->nom);
            $existing = Categorie::where('slug', $slug)->first();

            if ($existing) {
                $this->addError('nom', 'Une catégorie avec ce nom existe déjà.');
                return;
            }

            Categorie::create(['nom' => $this->nom, 'description' => $this->description, 'est_active' => $this->est_active, 'image' => $cheminImage]);
            session()->flash('success', 'Catégorie créée avec succès !');
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
        $categorie = Categorie::findOrFail($this->suppressionId);
        if ($categorie->plats()->count() > 0) {
            session()->flash('error', 'Impossible : cette catégorie contient des plats.');
            $this->afficherModalSuppression = false;
            return;
        }
        $categorie->delete();
        session()->flash('success', 'Catégorie supprimée avec succès !');
        $this->afficherModalSuppression = false;
        $this->suppressionId            = null;
    }

    public function toggleStatus(int $id): void
    {
        $categorie = Categorie::findOrFail($id);
        $categorie->update(['est_active' => ! $categorie->est_active]);
        session()->flash('success', 'Statut mis à jour !');
    }

    public function closeModal(): void
    {
        $this->afficherModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->nom           = '';
        $this->description   = '';
        $this->est_active    = true;
        $this->image         = null;
        $this->imageActuelle = null;
        $this->editingId     = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        $categories = Categorie::withCount('produits')
            ->when($this->search, fn($q) =>
                $q->where('nom', 'like', '%' . $this->search . '%')
            )
            ->when($this->filtreStatut !== '', fn($q) =>
                $q->where('est_active', $this->filtreStatut === '1')
            )
            ->latest()
            ->paginate(10);

        return view('livewire.categories.category-manager', compact('categories'));
    }
}

<?php

namespace App\Livewire\Categories;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryManager extends Component
{
    use WithPagination, WithFileUploads;

    // Liste
    public string $search       = '';
    public string $filterStatus = '';

    // Form
    public bool   $showModal    = false;
    public bool   $isEditing    = false;
    public ?int   $editingId    = null;

    // Champs
    public string  $name        = '';
    public string  $description = '';
    public bool    $is_active   = true;
    public         $image       = null;
    public ?string $currentImage = null;

    // Confirmation suppression
    public bool  $showDeleteModal = false;
    public ?int  $deletingId      = null;

    protected function rules(): array
    {
        return [
            'name'        => 'required|string|min:2|max:100',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
            'image'       => 'nullable|image|max:2048',
        ];
    }

    protected $messages = [
        'name.required' => 'Le nom de la catégorie est obligatoire.',
        'name.min'      => 'Le nom doit contenir au moins 2 caractères.',
        'image.image'   => 'Le fichier doit être une image.',
        'image.max'     => 'L\'image ne doit pas dépasser 2 Mo.',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // ── Ouvrir modal création ────────────────────────
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    // ── Ouvrir modal édition ─────────────────────────
    public function openEditModal(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->resetForm();
        $this->isEditing    = true;
        $this->editingId    = $id;
        $this->name         = $category->name;
        $this->description  = $category->description ?? '';
        $this->is_active    = $category->is_active;
        $this->currentImage = $category->image;
        $this->showModal    = true;
    }

    // ── Sauvegarder ──────────────────────────────────
    public function save(): void
    {
        $this->validate();

        $imagePath = $this->currentImage;

        if ($this->image) {
            $imagePath = $this->image->store('categories', 'public');
        }

        if ($this->isEditing) {
            $category = Category::findOrFail($this->editingId);
            $category->update([
                'name'        => $this->name,
                'description' => $this->description,
                'is_active'   => $this->is_active,
                'image'       => $imagePath,
            ]);
            session()->flash('success', 'Catégorie modifiée avec succès !');
        } else {
            // Vérifier si une catégorie supprimée avec ce nom existe déjà
            $slug = Str::slug($this->name);
            $existing = Category::withTrashed()
                            ->where('slug', $slug)
                            ->first();

            if ($existing && $existing->trashed()) {
                // Restaurer + mettre à jour
                $existing->restore();
                $existing->update([
                    'name'        => $this->name,
                    'description' => $this->description,
                    'is_active'   => $this->is_active,
                    'image'       => $imagePath ?? $existing->image,
                ]);
                session()->flash('success', 'Catégorie restaurée avec succès !');
            } else {
                Category::create([
                    'name'        => $this->name,
                    'description' => $this->description,
                    'is_active'   => $this->is_active,
                    'image'       => $imagePath,
                ]);
                session()->flash('success', 'Catégorie créée avec succès !');
            }
        }

        $this->closeModal();
        $this->resetPage();
    }

    // ── Confirmer suppression ─────────────────────────
    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    // ── Supprimer ─────────────────────────────────────
    public function delete(): void
    {
        $category = Category::findOrFail($this->deletingId);

        if ($category->products()->count() > 0) {
            session()->flash('error', 'Impossible de supprimer : cette catégorie contient des produits.');
            $this->showDeleteModal = false;
            return;
        }

        $category->delete();
        session()->flash('success', 'Catégorie supprimée avec succès !');
        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    // ── Toggle statut ─────────────────────────────────
    public function toggleStatus(int $id): void
    {
        $category = Category::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);
        session()->flash('success', 'Statut mis à jour !');
    }

    // ── Fermer modal ──────────────────────────────────
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    // ── Reset form ────────────────────────────────────
    private function resetForm(): void
    {
        $this->name         = '';
        $this->description  = '';
        $this->is_active    = true;
        $this->image        = null;
        $this->currentImage = null;
        $this->editingId    = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        $categories = Category::withCount('products')
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', '%' . $this->search . '%')
            )
            ->when($this->filterStatus !== '', fn($q) =>
                $q->where('is_active', $this->filterStatus === '1')
            )
            ->latest()
            ->paginate(10);

        return view('livewire.categories.category-manager', [
            'categories' => $categories,
        ]);
    }
}
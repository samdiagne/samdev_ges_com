<?php

namespace App\Livewire\Paliers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PalierGroupe;
use App\Models\Categorie;

class PalierGroupeManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filtreCategorie = '';

    // Modal formulaire
    public bool $afficherModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    // Champs du formulaire
    public ?int $categorie_id = null;
    public ?int $quantite_min = null;
    public ?float $prix_unitaire = null;
    public string $label = '';
    public bool $est_actif = true;

    // Modal suppression
    public bool $afficherModalSuppression = false;
    public ?int $suppressionId = null;

    protected function rules(): array
    {
        return [
            'categorie_id'  => 'required|exists:categories,id',
            'quantite_min'  => 'required|integer|min:2',
            'prix_unitaire' => 'required|numeric|min:1',
            'label'         => 'nullable|string|max:255',
            'est_actif'     => 'boolean',
        ];
    }

    protected $messages = [
        'categorie_id.required'  => 'Veuillez sélectionner une catégorie.',
        'categorie_id.exists'    => 'La catégorie sélectionnée est invalide.',
        'quantite_min.required'  => 'La quantité minimum est obligatoire.',
        'quantite_min.min'       => 'La quantité minimum doit être d\'au moins 2.',
        'prix_unitaire.required' => 'Le prix unitaire est obligatoire.',
        'prix_unitaire.min'      => 'Le prix unitaire doit être supérieur à 0.',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFiltreCategorie(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetValidation();
        $this->reset(['categorie_id', 'quantite_min', 'prix_unitaire', 'label', 'editingId']);
        $this->est_actif = true;
        $this->isEditing = false;
        $this->afficherModal = true;
    }

    public function openEditModal(int $id): void
    {
        $this->resetValidation();
        $palier = PalierGroupe::findOrFail($id);

        $this->editingId     = $palier->id;
        $this->categorie_id  = $palier->categorie_id;
        $this->quantite_min  = $palier->quantite_min;
        $this->prix_unitaire = (float) $palier->prix_unitaire;
        $this->label         = $palier->label ?? '';
        $this->est_actif     = (bool) $palier->est_actif;

        $this->isEditing     = true;
        $this->afficherModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        // Si aucun libellé saisi, génération automatique conviviale
        if (empty($validated['label'])) {
            $cat = Categorie::find($validated['categorie_id']);
            $nomCat = $cat ? $cat->nom : 'Groupe';
            $validated['label'] = "{$nomCat} ≥ {$validated['quantite_min']} unités";
        }

        if ($this->isEditing && $this->editingId) {
            $palier = PalierGroupe::findOrFail($this->editingId);
            $palier->update($validated);
            session()->flash('success', "Le palier {$palier->label} a été mis à jour.");
        } else {
            $palier = PalierGroupe::create($validated);
            session()->flash('success', "Le palier {$palier->label} a été créé avec succès.");
        }

        $this->afficherModal = false;
    }

    public function toggleActif(int $id): void
    {
        $palier = PalierGroupe::findOrFail($id);
        $palier->update(['est_actif' => ! $palier->est_actif]);
        session()->flash('success', "Statut du palier mis à jour.");
    }

    public function confirmDelete(int $id): void
    {
        $this->suppressionId = $id;
        $this->afficherModalSuppression = true;
    }

    public function delete(): void
    {
        if ($this->suppressionId) {
            $palier = PalierGroupe::findOrFail($this->suppressionId);
            $palier->delete();
            session()->flash('success', 'Le palier a été supprimé.');
        }

        $this->afficherModalSuppression = false;
        $this->suppressionId = null;
    }

    public function render()
    {
        $categories = Categorie::where('est_active', true)->orderBy('nom')->get();

        $paliers = PalierGroupe::with('categorie')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('label', 'like', '%' . $this->search . '%')
                        ->orWhereHas('categorie', fn($c) => $c->where('nom', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->filtreCategorie, fn($q) => $q->where('categorie_id', $this->filtreCategorie))
            ->orderBy('categorie_id')
            ->orderBy('quantite_min')
            ->paginate(15);

        return view('livewire.paliers.palier-groupe-manager', compact('paliers', 'categories'));
    }
}

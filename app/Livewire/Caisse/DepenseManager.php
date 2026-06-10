<?php

namespace App\Livewire\Caisse;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Depense;
use App\Models\DepenseCategorie;
use Illuminate\Support\Facades\Auth;

class DepenseManager extends Component
{
    use WithPagination, WithFileUploads;

    public string $search         = '';
    public string $filtreMonth    = '';
    public string $filtreCategorie = '';

    public bool   $afficherModal  = false;
    public bool   $isEditing      = false;
    public ?int   $editingId      = null;

    public int    $depense_categorie_id = 0;
    public string $description          = '';
    public string $montant              = '';
    public string $date_depense         = '';
    public string $mode_paiement        = 'cash';
    public string $notes                = '';
    public        $justificatif         = null;
    public ?string $justificatifActuel  = null;

    public bool   $afficherModalSuppression = false;
    public ?int   $suppressionId            = null;

    public bool   $montantVerrouille = false;

    protected function rules(): array
    {
        return [
            'depense_categorie_id' => 'required|exists:depense_categories,id',
            'description'          => 'required|string|min:2|max:255',
            'montant'              => 'required|numeric|min:1',
            'date_depense'         => 'required|date',
            'mode_paiement'        => 'required|string',
            'notes'                => 'nullable|string|max:500',
            'justificatif'         => 'nullable|image|max:2048',
        ];
    }

    protected $messages = [
        'depense_categorie_id.required' => 'Veuillez sélectionner une catégorie.',
        'description.required'          => 'La description est obligatoire.',
        'montant.required'              => 'Le montant est obligatoire.',
        'montant.min'                   => 'Le montant doit être supérieur à 0.',
        'date_depense.required'         => 'La date est obligatoire.',
    ];

    public function mount(): void
    {
        $this->filtreMonth  = now()->format('Y-m');
        $this->date_depense = now()->format('Y-m-d');
    }

    public function updatingSearch(): void { $this->resetPage(); }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isEditing    = false;
        $this->date_depense = now()->format('Y-m-d');
        $this->afficherModal = true;
    }

    public function openEditModal(int $id): void
    {
        $depense = Depense::findOrFail($id);
        $this->resetForm();
        $this->isEditing              = true;
        $this->editingId              = $id;
        $this->montantVerrouille      = ! $this->isDeletable($depense);
        $this->depense_categorie_id   = $depense->depense_categorie_id;
        $this->description            = $depense->description;
        $this->montant                = (float) $depense->montant;
        $this->date_depense           = $depense->date_depense->format('Y-m-d');
        $this->mode_paiement          = $depense->mode_paiement;
        $this->notes                  = $depense->notes ?? '';
        $this->justificatifActuel     = $depense->justificatif;
        $this->afficherModal          = true;
    }

    public function save(): void
    {
        $this->validate();

        $cheminJustificatif = $this->justificatifActuel;
        if ($this->justificatif) {
            $cheminJustificatif = $this->justificatif->store('justificatifs', 'public');
        }

        $data = [
            'depense_categorie_id' => $this->depense_categorie_id,
            'user_id'       => Auth::id(),
            'description'          => $this->description,
            'montant'              => (float) $this->montant,
            'date_depense'         => $this->date_depense,
            'mode_paiement'        => $this->mode_paiement,
            'notes'                => $this->notes ?: null,
            'justificatif'         => $cheminJustificatif,
        ];

        if ($this->isEditing) {
            $depense = Depense::findOrFail($this->editingId);
            if (! $this->isDeletable($depense)) unset($data['montant']);
            $depense->update($data);
            session()->flash('success', 'Dépense modifiée avec succès !');
        } else {
            Depense::create($data);
            session()->flash('success', 'Dépense enregistrée avec succès !');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $depense = Depense::findOrFail($id);
        if (! $this->isDeletable($depense)) {
            session()->flash('error', 'Cette dépense ne peut plus être supprimée (plus de 24h).');
            return;
        }
        $this->suppressionId            = $id;
        $this->afficherModalSuppression = true;
    }

    public function delete(): void
    {
        $depense = Depense::findOrFail($this->suppressionId);
        if (! $this->isDeletable($depense)) {
            session()->flash('error', 'Cette dépense ne peut plus être supprimée.');
            $this->afficherModalSuppression = false;
            return;
        }
        $depense->delete();
        session()->flash('success', 'Dépense supprimée avec succès !');
        $this->afficherModalSuppression = false;
        $this->suppressionId            = null;
        $this->resetPage();
    }

    private function isDeletable(Depense $depense): bool
    {
        return $depense->created_at->diffInHours(now()) <= 24;
    }

    public function closeModal(): void
    {
        $this->afficherModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->depense_categorie_id = 0;
        $this->description          = '';
        $this->montant              = '';
        $this->date_depense         = now()->format('Y-m-d');
        $this->mode_paiement        = 'cash';
        $this->notes                = '';
        $this->justificatif         = null;
        $this->justificatifActuel   = null;
        $this->editingId            = null;
        $this->montantVerrouille    = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        $depenses = Depense::with(['categorie', 'user'])
            ->when($this->search, fn($q) =>
                $q->where('description', 'like', '%' . $this->search . '%')
            )
            ->when($this->filtreCategorie, fn($q) =>
                $q->where('depense_categorie_id', $this->filtreCategorie)
            )
            ->when($this->filtreMonth, fn($q) =>
                $q->whereYear('date_depense', substr($this->filtreMonth, 0, 4))
                  ->whereMonth('date_depense', substr($this->filtreMonth, 5, 2))
            )
            ->latest('date_depense')
            ->paginate(15);

        $statsQuery = Depense::when($this->filtreMonth, fn($q) =>
            $q->whereYear('date_depense', substr($this->filtreMonth, 0, 4))
              ->whereMonth('date_depense', substr($this->filtreMonth, 5, 2))
        );

        $stats = [
            'total'          => $statsQuery->sum('montant'),
            'count'          => $statsQuery->count(),
            'par_categorie'  => DepenseCategorie::withSum(
                ['depenses' => fn($q) =>
                    $q->when($this->filtreMonth, fn($q2) =>
                        $q2->whereYear('date_depense', substr($this->filtreMonth, 0, 4))
                           ->whereMonth('date_depense', substr($this->filtreMonth, 5, 2))
                    )
                ], 'montant'
            )->having('depenses_sum_montant', '>', 0)->get(),
        ];

        $depenseCategories = DepenseCategorie::orderBy('nom')->get();

        return view('livewire.caisse.depense-manager',
            compact('depenses', 'stats', 'depenseCategories'));
    }
}

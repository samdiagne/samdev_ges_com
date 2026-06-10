<?php

namespace App\Livewire\Caisse;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Achat;
use App\Models\Fournisseur;

class AchatManager extends Component
{
    use WithPagination;

    public string $search          = '';
    public string $filtreMonth     = '';
    public string $filtreStatut    = '';
    public string $filtreFournisseur = '';

    public bool   $afficherModalDetail = false;
    public ?int   $voirAchatId         = null;

    public bool   $afficherModalSuppression = false;
    public ?int   $suppressionId            = null;

    public function mount(): void
    {
        $this->filtreMonth = now()->format('Y-m');
    }

    public function updatingSearch(): void { $this->resetPage(); }

    public function view(int $id): void
    {
        $this->voirAchatId         = $id;
        $this->afficherModalDetail = true;
    }

    public function confirmDelete(int $id): void
    {
        $this->suppressionId             = $id;
        $this->afficherModalSuppression  = true;
    }

    public function delete(): void
    {
        Achat::findOrFail($this->suppressionId)->delete();
        session()->flash('success', 'Achat supprimé avec succès !');
        $this->afficherModalSuppression = false;
        $this->suppressionId            = null;
        $this->resetPage();
    }

    public function render()
    {
        $achats = Achat::with(['fournisseur', 'user', 'lignes'])
            ->when($this->search, fn($q) =>
                $q->where('reference', 'like', '%' . $this->search . '%')
                  ->orWhereHas('fournisseur', fn($q2) =>
                      $q2->where('nom', 'like', '%' . $this->search . '%')
                  )
            )
            ->when($this->filtreStatut, fn($q) =>
                $q->where('statut', $this->filtreStatut)
            )
            ->when($this->filtreFournisseur, fn($q) =>
                $q->where('fournisseur_id', $this->filtreFournisseur)
            )
            ->when($this->filtreMonth, fn($q) =>
                $q->whereYear('date_achat', substr($this->filtreMonth, 0, 4))
                  ->whereMonth('date_achat', substr($this->filtreMonth, 5, 2))
            )
            ->latest('date_achat')
            ->paginate(15);

        $statsQuery = Achat::when($this->filtreMonth, fn($q) =>
            $q->whereYear('date_achat', substr($this->filtreMonth, 0, 4))
              ->whereMonth('date_achat', substr($this->filtreMonth, 5, 2))
        );

        $stats = [
            'count'  => $statsQuery->count(),
            'total'  => $statsQuery->sum('montant_total'),
            'paye'   => $statsQuery->sum('montant_paye'),
            'du'     => $statsQuery->sum('montant_du'),
        ];

        $fournisseurs = Fournisseur::where('est_actif', true)->orderBy('nom')->get(['id', 'nom']);

        $voirAchat = $this->voirAchatId
            ? Achat::with(['fournisseur', 'user', 'lignes'])->find($this->voirAchatId)
            : null;

        return view('livewire.caisse.achat-manager',
            compact('achats', 'stats', 'fournisseurs', 'voirAchat'));
    }
}

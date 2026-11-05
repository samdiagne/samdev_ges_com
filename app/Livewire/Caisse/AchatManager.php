<?php

namespace App\Livewire\Caisse;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Achat;
use App\Models\AchatReglement;
use App\Models\Fournisseur;
use App\Models\SessionCaisse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    // Modal de règlement / paiement
    public bool   $afficherModalPaiement = false;
    public ?int   $reglementAchatId      = null;
    public ?float $reglementMontant      = null;
    public string $reglementMode         = 'cash';
    public string $reglementNotes        = '';
    public ?int   $activeSessionId       = null;

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
        $achat = Achat::findOrFail($this->suppressionId);
        $achat->reglements()->delete();
        $achat->delete();

        session()->flash('success', 'Achat et règlements associés supprimés avec succès !');
        $this->afficherModalSuppression = false;
        $this->suppressionId            = null;
        $this->resetPage();
    }

    /**
     * Ouvre la modale pour enregistrer un versement / solder une facture.
     */
    public function ouvrirPaiement(int $achatId): void
    {
        $achat = Achat::findOrFail($achatId);
        $this->reglementAchatId = $achatId;
        $this->reglementMontant = (float) $achat->montant_du;
        $this->reglementMode    = $achat->mode_paiement ?: 'cash';
        $this->reglementNotes   = '';

        // Détection de la session active
        $session = SessionCaisse::where('user_id', Auth::id())
            ->where('statut', 'ouverte')
            ->latest('ouvert_le')
            ->first()
            ?? SessionCaisse::where('statut', 'ouverte')->latest('ouvert_le')->first();

        $this->activeSessionId = $session?->id;
        $this->afficherModalPaiement = true;
    }

    /**
     * Valide et enregistre un règlement lié à la session en cours.
     */
    public function validerReglement(): void
    {
        $achat = Achat::findOrFail($this->reglementAchatId);

        $this->validate([
            'reglementMontant' => 'required|numeric|min:1|max:' . $achat->montant_du,
            'reglementMode'    => 'required|in:cash,wave,om,carte',
            'reglementNotes'   => 'nullable|string|max:255',
        ], [
            'reglementMontant.required' => 'Le montant du versement est obligatoire.',
            'reglementMontant.numeric'  => 'Montant invalide.',
            'reglementMontant.min'      => 'Le montant doit être au moins de 1 F.',
            'reglementMontant.max'      => 'Le montant ne peut pas dépasser le solde restant dû (' . number_format($achat->montant_du, 0, ',', ' ') . ' F).',
        ]);

        $session = SessionCaisse::where('user_id', Auth::id())
            ->where('statut', 'ouverte')
            ->latest('ouvert_le')
            ->first()
            ?? SessionCaisse::where('statut', 'ouverte')->latest('ouvert_le')->first();

        if (! $session) {
            $this->addError('session', 'Aucune session de caisse n\'est actuellement ouverte. Veuillez ouvrir une caisse pour décaisser ce montant.');
            return;
        }

        DB::transaction(function () use ($achat, $session) {
            AchatReglement::create([
                'reference'         => AchatReglement::genererReference(),
                'achat_id'          => $achat->id,
                'session_caisse_id' => $session->id,
                'user_id'           => Auth::id(),
                'montant'           => $this->reglementMontant,
                'mode_paiement'     => $this->reglementMode,
                'date_reglement'    => now(),
                'notes'             => $this->reglementNotes ?: 'Paiement / Acompte décaissé sur session ' . $session->reference,
            ]);

            $achat->recalculerPaiement();

            // Mettre à jour le statut de l'achat si entièrement soldé
            if ($achat->montant_du <= 0 && $achat->statut !== 'recu') {
                $achat->update(['statut' => 'recu']);
            }
        });

        session()->flash('success', 'Règlement de ' . number_format($this->reglementMontant, 0, ',', ' ') . ' FCFA enregistré avec succès ! Déduit de la session ' . $session->reference . '.');

        $this->afficherModalPaiement = false;
        $this->reglementAchatId      = null;
        $this->reglementMontant      = null;
        $this->reglementNotes        = '';
    }

    public function render()
    {
        $achats = Achat::with(['fournisseur', 'user', 'lignes', 'reglements'])
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
            ? Achat::with(['fournisseur', 'user', 'lignes', 'reglements.sessionCaisse', 'reglements.user'])->find($this->voirAchatId)
            : null;

        $achatEnReglement = $this->reglementAchatId
            ? Achat::with('fournisseur')->find($this->reglementAchatId)
            : null;

        $activeSession = SessionCaisse::where('user_id', Auth::id())
            ->where('statut', 'ouverte')
            ->latest('ouvert_le')
            ->first()
            ?? SessionCaisse::where('statut', 'ouverte')->latest('ouvert_le')->first();

        return view('livewire.caisse.achat-manager',
            compact('achats', 'stats', 'fournisseurs', 'voirAchat', 'achatEnReglement', 'activeSession'));
    }
}

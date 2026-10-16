<?php

namespace App\Livewire\Caisse;

use Livewire\Component;
use App\Models\SessionCaisse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RapportJournalier extends Component
{
    public string $date;
    public ?int   $selectedUserId = null;
    public ?int   $sessionDetailId = null;

    public function mount(): void
    {
        $this->date = now()->toDateString();
        $this->selectedUserId = Auth::id();
    }

    public function setDateAujourdhui(): void
    {
        $this->date = now()->toDateString();
    }

    public function setDateHier(): void
    {
        $this->date = now()->subDay()->toDateString();
    }

    public function toggleDetail(int $sessionId): void
    {
        $this->sessionDetailId = ($this->sessionDetailId === $sessionId) ? null : $sessionId;
    }

    public function render()
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser->isAdmin();

        // Si non-admin, on force sur l'utilisateur connecté
        if (! $isAdmin) {
            $this->selectedUserId = $currentUser->id;
        }

        $parsedDate = Carbon::parse($this->date);

        $query = SessionCaisse::with(['user', 'ventes.client', 'depenses.categorie'])
            ->whereDate('ouvert_le', $parsedDate)
            ->when($this->selectedUserId, fn($q) => $q->where('user_id', $this->selectedUserId))
            ->orderBy('ouvert_le', 'desc');

        $sessions = $query->get();

        // Calculs consolidés
        $stats = [
            'total_ca'        => 0,
            'ca_cash'         => 0,
            'ca_wave'         => 0,
            'ca_om'           => 0,
            'ca_carte'        => 0,
            'total_depenses'  => 0,
            'fond_total'      => 0,
            'theorique_cash'  => 0,
            'reel_especes'    => 0,
            'ecart_net'       => 0,
            'nb_ventes'       => 0,
        ];

        foreach ($sessions as $session) {
            $totaux = $session->estOuverte() ? $session->totaux_en_direct : [
                'total_ventes'   => (float) $session->total_ventes,
                'ventes_cash'    => (float) $session->total_ventes_cash,
                'ventes_wave'    => (float) $session->total_ventes_wave,
                'ventes_om'      => (float) $session->total_ventes_om,
                'ventes_carte'   => (float) $session->total_ventes_carte,
                'total_depenses' => (float) $session->total_depenses,
                'theorique_cash' => (float) $session->solde_theorique_especes,
            ];

            $stats['total_ca']       += $totaux['total_ventes'];
            $stats['ca_cash']        += $totaux['ventes_cash'];
            $stats['ca_wave']        += $totaux['ventes_wave'];
            $stats['ca_om']          += $totaux['ventes_om'];
            $stats['ca_carte']       += $totaux['ventes_carte'];
            $stats['total_depenses'] += $totaux['total_depenses'];
            $stats['fond_total']     += (float) $session->fond_caisse_ouverture;
            $stats['theorique_cash'] += $totaux['theorique_cash'];
            $stats['reel_especes']   += (float) ($session->montant_reel_especes ?? $totaux['theorique_cash']);
            $stats['ecart_net']      += (float) ($session->ecart ?? 0);
            $stats['nb_ventes']      += $session->ventes->count();
        }

        $caissiers = $isAdmin ? User::role(['admin', 'gestionnaire'])->orderBy('name')->get() : collect();

        return view('livewire.caisse.rapport-journalier', compact('sessions', 'stats', 'caissiers', 'isAdmin'));
    }
}

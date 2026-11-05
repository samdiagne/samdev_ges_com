<?php

namespace App\Http\Controllers;

use App\Models\SessionCaisse;
use App\Models\User;
use App\Models\Parametre;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SessionCaisseController extends Controller
{
    /**
     * Génère et télécharge le Ticket Z / Fiche de clôture d'une session de caisse en PDF.
     */
    public function ticketZ(SessionCaisse $session)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && $session->user_id !== $user->id) {
            abort(403, 'Accès non autorisé à cette clôture.');
        }

        $session->load(['user', 'ventes.client', 'depenses.categorie', 'achats.fournisseur', 'achatReglements.achat.fournisseur']);

        $parametres = Parametre::first();
        $logoBase64 = $this->getLogoBase64();

        return Pdf::loadView('pdf.caisse.ticket-z', compact('session', 'parametres', 'logoBase64'))
            ->setPaper('a4', 'portrait')
            ->setOptions(['defaultFont' => 'DejaVu Sans', 'isHtml5ParserEnabled' => true])
            ->download("cloture-caisse-{$session->reference}.pdf");
    }

    /**
     * Génère et télécharge le Rapport Quotidien de l'utilisateur connecté (ou caissier sélectionné).
     */
    public function rapportJournalierPdf(Request $request)
    {
        $currentUser = Auth::user();
        $dateStr = $request->input('date', now()->toDateString());
        $date = Carbon::parse($dateStr);

        // Si l'utilisateur n'est pas admin, il ne peut voir que son propre rapport
        $targetUserId = (! $currentUser->isAdmin())
            ? $currentUser->id
            : ($request->filled('user_id') ? (int) $request->user_id : $currentUser->id);

        $targetUser = User::find($targetUserId) ?? $currentUser;

        // Récupérer les sessions du jour pour cet utilisateur
        $sessions = SessionCaisse::with(['ventes', 'depenses', 'achats.fournisseur', 'achatReglements.achat.fournisseur'])
            ->where('user_id', $targetUserId)
            ->where(function ($q) use ($date) {
                $q->whereDate('ouvert_le', $date);
                if ($date->isToday()) {
                    $q->orWhere('statut', 'ouverte');
                }
            })
            ->orderBy('ouvert_le', 'asc')
            ->get();

        // Calculs consolidés de la journée pour cet utilisateur
        $stats = [
            'nb_sessions'               => $sessions->count(),
            'total_ca'                  => 0,
            'ca_cash'                   => 0,
            'ca_wave'                   => 0,
            'ca_om'                     => 0,
            'ca_carte'                  => 0,
            'total_depenses'            => 0,
            'total_achats'              => 0,
            'total_sorties'             => 0,
            'fond_total'                => 0,
            'fond_wave_total'           => 0,
            'monnaie_rendue_wave_total' => 0,
            'reel_especes'              => 0,
            'theorique_cash'            => 0,
            'ecart_net'                 => 0,
            'reel_wave'                 => 0,
            'theorique_wave'            => 0,
            'ecart_wave_net'            => 0,
        ];

        foreach ($sessions as $session) {
            $totaux = $session->estOuverte() ? $session->totaux_en_direct : [
                'total_ventes'        => (float) $session->total_ventes,
                'ventes_cash'         => (float) $session->total_ventes_cash,
                'ventes_wave'         => (float) $session->total_ventes_wave,
                'ventes_om'           => (float) $session->total_ventes_om,
                'ventes_carte'        => (float) $session->total_ventes_carte,
                'total_depenses'      => (float) $session->total_depenses,
                'total_achats'        => (float) ($session->total_achats ?? 0),
                'monnaie_rendue_wave' => (float) $session->total_monnaie_rendue_wave,
                'theorique_cash'      => (float) $session->solde_theorique_especes,
                'theorique_wave'      => (float) $session->solde_theorique_wave,
            ];

            $stats['total_ca']                  += $totaux['total_ventes'];
            $stats['ca_cash']                   += $totaux['ventes_cash'];
            $stats['ca_wave']                   += $totaux['ventes_wave'];
            $stats['ca_om']                     += $totaux['ventes_om'];
            $stats['ca_carte']                  += $totaux['ventes_carte'];
            $stats['total_depenses']            += $totaux['total_depenses'];
            $stats['total_achats']              += ($totaux['total_achats'] ?? 0);
            $stats['total_sorties']             += $totaux['total_depenses'] + ($totaux['total_achats'] ?? 0);
            $stats['fond_total']                += (float) $session->fond_caisse_ouverture;
            $stats['fond_wave_total']           += (float) ($session->fond_caisse_wave_ouverture ?? 0);
            $stats['monnaie_rendue_wave_total'] += (float) ($totaux['monnaie_rendue_wave'] ?? 0);
            $stats['theorique_cash']            += $totaux['theorique_cash'];
            $stats['theorique_wave']            += (float) ($totaux['theorique_wave'] ?? 0);
            $stats['reel_especes']              += (float) ($session->montant_reel_especes ?? $totaux['theorique_cash']);
            $stats['reel_wave']                 += (float) ($session->montant_reel_wave ?? ($totaux['theorique_wave'] ?? 0));
            $stats['ecart_net']                 += (float) ($session->ecart ?? 0);
            $stats['ecart_wave_net']            += (float) ($session->ecart_wave ?? 0);
        }

        $parametres = Parametre::first();
        $logoBase64 = $this->getLogoBase64();

        return Pdf::loadView('pdf.caisse.rapport-journalier', compact(
            'sessions',
            'stats',
            'targetUser',
            'date',
            'parametres',
            'logoBase64'
        ))
        ->setPaper('a4', 'portrait')
        ->setOptions(['defaultFont' => 'DejaVu Sans', 'isHtml5ParserEnabled' => true])
        ->download("rapport-caisse-{$targetUser->name}-{$date->format('Y-m-d')}.pdf");
    }

    private function getLogoBase64(): ?string
    {
        $parametres = Parametre::first();
        if (! $parametres?->logo) return null;

        $path = Storage::disk('public')->path($parametres->logo);
        if (! file_exists($path)) return null;

        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = base64_encode(file_get_contents($path));
        return "data:image/{$type};base64,{$data}";
    }
}

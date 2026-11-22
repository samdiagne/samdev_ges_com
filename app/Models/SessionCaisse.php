<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class SessionCaisse extends Model
{
    protected $table = 'sessions_caisse';

    protected $fillable = [
        'reference',
        'user_id',
        'fond_caisse_ouverture',
        'fond_caisse_wave_ouverture',
        'ouvert_le',
        'ferme_le',
        'statut',
        'total_ventes_cash',
        'total_ventes_wave',
        'total_ventes_om',
        'total_ventes_carte',
        'total_ventes',
        'total_monnaie_rendue_wave',
        'total_depenses_cash',
        'total_depenses',
        'total_achats_cash',
        'total_achats_wave',
        'total_achats',
        'solde_theorique_especes',
        'solde_theorique_wave',
        'montant_reel_especes',
        'montant_reel_wave',
        'ecart',
        'ecart_wave',
        'notes_fermeture',
    ];

    protected function casts(): array
    {
        return [
            'fond_caisse_ouverture'      => 'decimal:2',
            'fond_caisse_wave_ouverture' => 'decimal:2',
            'ouvert_le'                  => 'datetime',
            'ferme_le'                   => 'datetime',
            'total_ventes_cash'          => 'decimal:2',
            'total_ventes_wave'          => 'decimal:2',
            'total_ventes_om'            => 'decimal:2',
            'total_ventes_carte'         => 'decimal:2',
            'total_ventes'               => 'decimal:2',
            'total_monnaie_rendue_wave'  => 'decimal:2',
            'total_depenses_cash'        => 'decimal:2',
            'total_depenses'             => 'decimal:2',
            'total_achats_cash'          => 'decimal:2',
            'total_achats_wave'          => 'decimal:2',
            'total_achats'               => 'decimal:2',
            'solde_theorique_especes'    => 'decimal:2',
            'solde_theorique_wave'       => 'decimal:2',
            'montant_reel_especes'       => 'decimal:2',
            'montant_reel_wave'          => 'decimal:2',
            'ecart'                      => 'decimal:2',
            'ecart_wave'                 => 'decimal:2',
        ];
    }

    // ── Relations ──────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class, 'session_caisse_id');
    }

    public function depenses(): HasMany
    {
        return $this->hasMany(Depense::class, 'session_caisse_id');
    }

    public function achats(): HasMany
    {
        return $this->hasMany(Achat::class, 'session_caisse_id');
    }

    public function achatReglements(): HasMany
    {
        return $this->hasMany(AchatReglement::class, 'session_caisse_id');
    }

    // ── Méthodes & Helpers ─────────────────────────────────

    public function estOuverte(): bool
    {
        return $this->statut === 'ouverte';
    }

    public function duree(): string
    {
        $fin = $this->ferme_le ?? now();
        $minutes = $this->ouvert_le ? (int) $this->ouvert_le->diffInMinutes($fin) : 0;
        $heures = intdiv($minutes, 60);
        $resteMin = $minutes % 60;

        if ($heures > 0) {
            return "{$heures}h " . str_pad((string)$resteMin, 2, '0', STR_PAD_LEFT) . "min";
        }

        return "{$resteMin} min";
    }

    /**
     * Calcule en temps réel les totaux pour une session en cours.
     */
    public function getTotauxEnDirectAttribute(): array
    {
        if (! $this->estOuverte()) {
            return [
                'ventes_cash'         => (float) $this->total_ventes_cash,
                'ventes_wave'         => (float) $this->total_ventes_wave,
                'ventes_om'           => (float) $this->total_ventes_om,
                'ventes_carte'        => (float) $this->total_ventes_carte,
                'total_ventes'        => (float) $this->total_ventes,
                'depenses_cash'       => (float) $this->total_depenses_cash,
                'total_depenses'      => (float) $this->total_depenses,
                'achats_cash'         => (float) ($this->total_achats_cash ?? 0),
                'achats_wave'         => (float) ($this->total_achats_wave ?? 0),
                'total_achats'        => (float) ($this->total_achats ?? 0),
                'monnaie_rendue_wave' => (float) $this->total_monnaie_rendue_wave,
                'theorique_cash'      => (float) $this->solde_theorique_especes,
                'theorique_wave'      => (float) $this->solde_theorique_wave,
            ];
        }

        $ventesQuery = $this->ventes()->where('statut', 'completee');
        $ventesCash  = (float) (clone $ventesQuery)->sum(\Illuminate\Support\Facades\DB::raw("CASE WHEN mode_paiement = 'multiple' THEN montant_cash WHEN mode_paiement = 'cash' THEN total ELSE 0 END"));
        $ventesWave  = (float) (clone $ventesQuery)->sum(\Illuminate\Support\Facades\DB::raw("CASE WHEN mode_paiement = 'multiple' THEN montant_wave WHEN mode_paiement = 'wave' THEN total ELSE 0 END"));
        $ventesOm    = (float) (clone $ventesQuery)->sum(\Illuminate\Support\Facades\DB::raw("CASE WHEN mode_paiement = 'multiple' THEN montant_om WHEN mode_paiement = 'om' THEN total ELSE 0 END"));
        $ventesCarte = (float) (clone $ventesQuery)->sum(\Illuminate\Support\Facades\DB::raw("CASE WHEN mode_paiement = 'multiple' THEN montant_carte WHEN mode_paiement IN ('carte', 'card') THEN total ELSE 0 END"));
        $totalVentes = (float) $ventesQuery->sum('total');

        // Monnaies rendues au client via Wave (sur ventes en espèces par manque de monnaie)
        $monnaieRendueWave = (float) (clone $ventesQuery)->sum('monnaie_rendue_wave');

        $depensesQuery = $this->depenses();
        $depensesCash  = (float) (clone $depensesQuery)->where('mode_paiement', 'cash')->sum('montant');
        $depensesWave  = (float) (clone $depensesQuery)->where('mode_paiement', 'wave')->sum('montant');
        $totalDepenses = (float) $depensesQuery->sum('montant');

        // Sorties de caisse réelles liées aux règlements d'achats effectués pendant cette session
        $reglementsQuery = $this->achatReglements();
        $achatsCash  = (float) (clone $reglementsQuery)->where('mode_paiement', 'cash')->sum('montant');
        $achatsWave  = (float) (clone $reglementsQuery)->where('mode_paiement', 'wave')->sum('montant');
        $totalAchats = (float) $reglementsQuery->sum('montant');

        $fondCash = (float) $this->fond_caisse_ouverture;
        $fondWave = (float) ($this->fond_caisse_wave_ouverture ?? 0);

        // Tiroir espèces : fond + ventes cash + monnaie non décaissée - dépenses cash - achats cash
        $theoriqueCash = round($fondCash + $ventesCash + $monnaieRendueWave - $depensesCash - $achatsCash, 2);

        // Compte Wave : fond Wave + ventes Wave - monnaie rendue Wave - dépenses Wave - achats Wave
        $theoriqueWave = round($fondWave + $ventesWave - $monnaieRendueWave - $depensesWave - $achatsWave, 2);

        return [
            'ventes_cash'         => $ventesCash,
            'ventes_wave'         => $ventesWave,
            'ventes_om'           => $ventesOm,
            'ventes_carte'        => $ventesCarte,
            'total_ventes'        => $totalVentes,
            'depenses_cash'       => $depensesCash,
            'depenses_wave'       => $depensesWave,
            'total_depenses'      => $totalDepenses,
            'achats_cash'         => $achatsCash,
            'achats_wave'         => $achatsWave,
            'total_achats'        => $totalAchats,
            'monnaie_rendue_wave' => $monnaieRendueWave,
            'theorique_cash'      => $theoriqueCash,
            'theorique_wave'      => $theoriqueWave,
        ];
    }

    public static function genererReference(): string
    {
        $prefix = 'SES-' . date('Ymd') . '-';
        $derniere = self::where('reference', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        $num = $derniere ? (intval(substr($derniere->reference, -3)) + 1) : 1;
        return $prefix . str_pad((string)$num, 3, '0', STR_PAD_LEFT);
    }
}

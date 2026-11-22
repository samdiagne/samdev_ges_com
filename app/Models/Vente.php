<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vente extends Model
{
    use SoftDeletes;

    protected $table = 'ventes';

    protected $fillable = [
        'reference',
        'client_id',
        'user_id',
        'session_caisse_id',
        'source',
        'statut',
        'total',
        'remise',
        'montant_recu',
        'monnaie_rendue',
        'monnaie_rendue_especes',
        'monnaie_rendue_wave',
        'mode_paiement',
        'montant_cash',
        'montant_wave',
        'montant_om',
        'montant_carte',
        'notes',
        'vendu_le',
    ];

    protected function casts(): array
    {
        return [
            'total'                  => 'decimal:2',
            'remise'                 => 'decimal:2',
            'montant_recu'           => 'decimal:2',
            'monnaie_rendue'         => 'decimal:2',
            'monnaie_rendue_especes' => 'decimal:2',
            'monnaie_rendue_wave'    => 'decimal:2',
            'montant_cash'           => 'decimal:2',
            'montant_wave'           => 'decimal:2',
            'montant_om'             => 'decimal:2',
            'montant_carte'          => 'decimal:2',
            'vendu_le'               => 'datetime',
        ];
    }

    // ── Relations ─────────────────────────────────

    public function sessionCaisse(): BelongsTo
    {
        return $this->belongsTo(SessionCaisse::class, 'session_caisse_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(VenteLigne::class, 'vente_id');
    }

    public function facture(): HasOne
    {
        return $this->hasOne(Facture::class, 'vente_id');
    }

    public function commande(): HasOne
    {
        return $this->hasOne(Commande::class, 'vente_id');
    }

    // ── Helpers Paiement ─────────────────────────

    public function estPaiementMultiple(): bool
    {
        return $this->mode_paiement === 'multiple';
    }

    public function getModePaiementLibelleAttribute(): string
    {
        return match ($this->mode_paiement) {
            'cash'     => 'Cash (Espèces)',
            'wave'     => 'Wave',
            'om'       => 'Orange Money',
            'mix'      => 'Mixx by Yass',
            'carte', 'card' => 'Carte bancaire',
            'a_regler' => 'À régler',
            'multiple' => 'Mixte / Multiple',
            default    => ucfirst($this->mode_paiement ?? '—'),
        };
    }

    public function getPaiementsDecomposesAttribute(): array
    {
        $parts = [];
        if ((float) $this->montant_cash > 0) {
            $parts['cash'] = (float) $this->montant_cash;
        }
        if ((float) $this->montant_wave > 0) {
            $parts['wave'] = (float) $this->montant_wave;
        }
        if ((float) $this->montant_om > 0) {
            $parts['om'] = (float) $this->montant_om;
        }
        if ((float) $this->montant_carte > 0) {
            $parts['carte'] = (float) $this->montant_carte;
        }
        return $parts;
    }
}

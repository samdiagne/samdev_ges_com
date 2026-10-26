<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Achat extends Model
{
    use SoftDeletes;

    protected $table = 'achats';

    protected $fillable = [
        'reference',
        'fournisseur_id',
        'user_id',
        'session_caisse_id',
        'statut',
        'montant_total',
        'montant_paye',
        'montant_du',
        'mode_paiement',
        'date_achat',
        'date_livraison_prevue',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'montant_total'          => 'decimal:2',
            'montant_paye'           => 'decimal:2',
            'montant_du'             => 'decimal:2',
            'date_achat'             => 'date',
            'date_livraison_prevue'  => 'date',
        ];
    }

    // ── Relations ─────────────────────────────────

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class, 'fournisseur_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(AchatLigne::class, 'achat_id');
    }

    public function sessionCaisse(): BelongsTo
    {
        return $this->belongsTo(SessionCaisse::class, 'session_caisse_id');
    }
}

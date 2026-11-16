<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class PalierGroupe extends Model
{
    protected $table = 'paliers_groupe';

    protected $fillable = [
        'categorie_id',
        'quantite_min',
        'prix_unitaire',
        'label',
        'est_actif',
    ];

    protected function casts(): array
    {
        return [
            'quantite_min'  => 'integer',
            'prix_unitaire' => 'decimal:2',
            'est_actif'     => 'boolean',
        ];
    }

    // ── Relations ────────────────────────────────────

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    // ── Scopes ───────────────────────────────────────

    public function scopeActifs(Builder $query): Builder
    {
        return $query->where('est_actif', true);
    }
}

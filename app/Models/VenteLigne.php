<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VenteLigne extends Model
{
    protected $table = 'vente_lignes';

    protected $fillable = [
        'vente_id',
        'produit_id',
        'nom_produit',
        'prix_unitaire',
        'quantite',
        'remise',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'prix_unitaire' => 'decimal:2',
            'remise'        => 'decimal:2',
            'total'         => 'decimal:2',
        ];
    }

    // ── Relations ─────────────────────────────────

    public function vente(): BelongsTo
    {
        return $this->belongsTo(Vente::class, 'vente_id');
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }
}

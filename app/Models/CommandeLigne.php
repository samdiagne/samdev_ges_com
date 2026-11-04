<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommandeLigne extends Model
{
    protected $table = 'commande_lignes';

    protected $fillable = [
        'commande_id',
        'produit_id',
        'nom_produit',
        'details',
        'prix_unitaire',
        'quantite',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'prix_unitaire' => 'decimal:2',
            'total'         => 'decimal:2',
        ];
    }

    // ── Relations ─────────────────────────────────

    public function commande(): BelongsTo
    {
        return $this->belongsTo(Commande::class, 'commande_id');
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }
}

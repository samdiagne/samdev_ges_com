<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AchatLigne extends Model
{
    protected $table = 'achat_lignes';

    protected $fillable = [
        'achat_id',
        'description',
        'prix_unitaire',
        'quantite',
        'unite',
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

    public function achat(): BelongsTo
    {
        return $this->belongsTo(Achat::class, 'achat_id');
    }
}

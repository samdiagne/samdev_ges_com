<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementStock extends Model
{
    protected $table = 'mouvements_stock';

    protected $fillable = [
        'plat_id',
        'user_id',
        'type',
        'source',
        'source_id',
        'stock_avant',
        'quantite_changement',
        'stock_apres',
        'motif',
        'effectue_le',
    ];

    protected function casts(): array
    {
        return [
            'effectue_le' => 'datetime',
        ];
    }

    // ── Relations ─────────────────────────────────

    public function plat(): BelongsTo
    {
        return $this->belongsTo(Plat::class, 'plat_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

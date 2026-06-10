<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Avis extends Model
{
    protected $table = 'avis';

    protected $fillable = [
        'plat_id',
        'user_id',
        'nom_auteur',
        'note',
        'commentaire',
        'est_approuve',
    ];

    protected function casts(): array
    {
        return [
            'note'         => 'integer',
            'est_approuve' => 'boolean',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AchatReglement extends Model
{
    use SoftDeletes;

    protected $table = 'achat_reglements';

    protected $fillable = [
        'reference',
        'achat_id',
        'session_caisse_id',
        'user_id',
        'montant',
        'mode_paiement',
        'date_reglement',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'montant'        => 'decimal:2',
            'date_reglement' => 'datetime',
        ];
    }

    public static function genererReference(): string
    {
        $prefix = 'REG-' . date('Ymd') . '-';
        $dernier = static::withTrashed()
            ->where('reference', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->value('reference');

        $num = $dernier ? ((int) substr($dernier, -4)) + 1 : 1;
        return $prefix . str_pad((string)$num, 4, '0', STR_PAD_LEFT);
    }

    // ── Relations ─────────────────────────────────

    public function achat(): BelongsTo
    {
        return $this->belongsTo(Achat::class, 'achat_id');
    }

    public function sessionCaisse(): BelongsTo
    {
        return $this->belongsTo(SessionCaisse::class, 'session_caisse_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

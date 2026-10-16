<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depense extends Model
{
    use SoftDeletes;

    protected $table = 'depenses';

    protected $fillable = [
        'depense_categorie_id',
        'user_id',
        'session_caisse_id',
        'description',
        'montant',
        'date_depense',
        'mode_paiement',
        'justificatif',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'montant'      => 'decimal:2',
            'date_depense' => 'date',
        ];
    }

    // ── Relations ─────────────────────────────────

    public function sessionCaisse(): BelongsTo
    {
        return $this->belongsTo(SessionCaisse::class, 'session_caisse_id');
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(DepenseCategorie::class, 'depense_categorie_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

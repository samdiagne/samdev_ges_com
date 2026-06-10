<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference', 'client_id', 'user_id', 'status',
        'client_nom', 'client_telephone',
        'sous_total', 'remise', 'total',
        'notes', 'valide_jusqua', 'envoye_whatsapp', 'date_devis',
    ];

    protected function casts(): array
    {
        return [
            'sous_total'       => 'decimal:2',
            'remise'           => 'decimal:2',
            'total'            => 'decimal:2',
            'envoye_whatsapp'  => 'boolean',
            'valide_jusqua'    => 'date',
            'date_devis'       => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function getNomClientAttribute(): string
    {
        return $this->client?->nom ?? $this->client_nom ?? 'Client anonyme';
    }

    public function isExpired(): bool
    {
        return $this->valide_jusqua && $this->valide_jusqua->isPast()
            && !in_array($this->status, ['accepted', 'refused']);
    }

    public static function generateReference(): string
    {
        $last = static::withTrashed()->latest()->first();
        $num  = $last ? (intval(substr($last->reference, -4)) + 1) : 1;
        return 'DEV-' . date('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}

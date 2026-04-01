<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference', 'customer_id', 'user_id', 'status',
        'customer_name', 'customer_phone',
        'subtotal_ht', 'tva_amount', 'total_ttc',
        'discount', 'tva_rate', 'notes',
        'valid_until', 'sent_whatsapp', 'quote_date',
    ];

    protected function casts(): array
    {
        return [
            'subtotal_ht'   => 'decimal:2',
            'tva_amount'    => 'decimal:2',
            'total_ttc'     => 'decimal:2',
            'discount'      => 'decimal:2',
            'tva_rate'      => 'decimal:2',
            'sent_whatsapp' => 'boolean',
            'valid_until'   => 'date',
            'quote_date'    => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function getClientNameAttribute(): string
    {
        return $this->customer?->name ?? $this->customer_name ?? 'Client anonyme';
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast()
            && !in_array($this->status, ['accepted', 'refused']);
    }

    public static function generateReference(): string
    {
        $last = static::withTrashed()->latest()->first();
        $num  = $last ? (intval(substr($last->reference, -4)) + 1) : 1;
        return 'DEV-' . date('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
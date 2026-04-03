<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference', 'user_id',
        'customer_name', 'customer_email', 'customer_phone',
        'delivery_address', 'city', 'notes',
        'subtotal', 'delivery_fee', 'total',
        'payment_method', 'payment_status',
        'paydunya_token', 'paydunya_invoice_token',
        'status', 'sale_id',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'     => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'total'        => 'decimal:2',
        ];
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public static function generateReference(): string
    {
        $last = static::withTrashed()->latest()->first();
        $num  = $last ? (intval(substr($last->reference, -4)) + 1) : 1;
        return 'CMD-' . date('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'En attente',
            'confirmed'  => 'Confirmée',
            'processing' => 'En préparation',
            'shipped'    => 'Expédiée',
            'delivered'  => 'Livrée',
            'cancelled'  => 'Annulée',
            default      => $this->status,
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'pending'   => 'En attente',
            'paid'      => 'Payé',
            'failed'    => 'Échoué',
            default     => $this->payment_status,
        };
    }

    public function getPaymentLabelAttribute(): string
    {
        return match($this->payment_method) {
            'wave'     => 'Wave',
            'om'       => 'Orange Money',
            'mix'      => 'Mix by Yass',
            'djamo'    => 'Djamo',
            'delivery' => 'À la livraison',
            'card'     => 'Carte bancaire',
            default    => $this->payment_method,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'bg-secondary/10 text-secondary',
            'confirmed'  => 'bg-primary/10 text-primary',
            'processing' => 'bg-accent/10 text-primary',
            'shipped'    => 'bg-primary/20 text-primary',
            'delivered'  => 'bg-green-100 text-green-700',
            'cancelled'  => 'bg-red-100 text-red-700',
            default      => 'bg-gray-100 text-gray-700',
        };
    }

    // Alias pour compatibilité vue Livewire
    public function getDeliveryTypeAttribute(): string
    {
        return $this->payment_method;
    }

    public function getDeliveryCityAttribute(): string
    {
        return $this->city;
    }
}
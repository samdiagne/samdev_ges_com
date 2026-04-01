<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference', 'customer_id', 'user_id', 'source', 'status',
        'subtotal_ht', 'tva_amount', 'total_ttc', 'discount',
        'amount_paid', 'change_given', 'payment_method', 'notes', 'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal_ht'  => 'decimal:2',
            'tva_amount'   => 'decimal:2',
            'total_ttc'    => 'decimal:2',
            'discount'     => 'decimal:2',
            'amount_paid'  => 'decimal:2',
            'change_given' => 'decimal:2',
            'sold_at'      => 'datetime',
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
        return $this->hasMany(SaleItem::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference', 'supplier_id', 'user_id', 'status',
        'total_amount', 'amount_paid', 'amount_due',
        'payment_method', 'purchase_date', 'expected_date', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_amount'  => 'decimal:2',
            'amount_paid'   => 'decimal:2',
            'amount_due'    => 'decimal:2',
            'purchase_date' => 'date',
            'expected_date' => 'date',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id', 'product_id', 'product_name',
        'unit_price_ht', 'unit_price_ttc', 'quantity',
        'discount', 'total_ht', 'total_ttc',
    ];

    protected function casts(): array
    {
        return [
            'unit_price_ht'  => 'decimal:2',
            'unit_price_ttc' => 'decimal:2',
            'discount'       => 'decimal:2',
            'total_ht'       => 'decimal:2',
            'total_ttc'      => 'decimal:2',
        ];
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id', 'product_id', 'description',
        'unit_price_ht', 'unit_price_ttc', 'quantity',
        'tva_rate', 'discount', 'total_ht', 'total_ttc',
        'is_free_line',
    ];

    protected function casts(): array
    {
        return [
            'unit_price_ht'  => 'decimal:2',
            'unit_price_ttc' => 'decimal:2',
            'tva_rate'       => 'decimal:2',
            'discount'       => 'decimal:2',
            'total_ht'       => 'decimal:2',
            'total_ttc'      => 'decimal:2',
            'is_free_line'   => 'boolean',
        ];
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
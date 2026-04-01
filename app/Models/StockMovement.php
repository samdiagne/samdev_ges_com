<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'user_id', 'type', 'source',
        'source_id', 'quantity_before', 'quantity_change',
        'quantity_after', 'reason', 'moved_at',
    ];

    protected function casts(): array
    {
        return ['moved_at' => 'datetime'];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
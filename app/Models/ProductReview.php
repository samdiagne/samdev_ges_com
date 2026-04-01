<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    protected $fillable = [
        'product_id', 'user_id', 'author_name',
        'author_email', 'rating', 'comment', 'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
            'rating'      => 'integer',
        ];
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
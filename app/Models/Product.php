<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'supplier_id', 'name', 'slug', 'sku',
        'description', 'short_description', 'purchase_price',
        'selling_price', 'selling_price_ttc', 'old_price', 'stock_quantity',
        'stock_alert_threshold', 'unit', 'brand', 'model',
        'is_active', 'is_featured', 'has_tva', 'main_image',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price'       => 'decimal:2',
            'selling_price'        => 'decimal:2',
            'selling_price_ttc'    => 'decimal:2',
            'old_price'            => 'decimal:2',
            'is_active'            => 'boolean',
            'is_featured'          => 'boolean',
            'has_tva'              => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
            if (empty($product->sku)) {
                $product->sku = 'SKU-' . strtoupper(Str::random(8));
            }
            // Calcul automatique TTC
            if ($product->has_tva) {
                $tva = CompanySetting::first()?->tva_rate ?? 18;
                $product->selling_price_ttc = $product->selling_price * (1 + $tva / 100);
            } else {
                $product->selling_price_ttc = $product->selling_price;
            }
        });

        static::updating(function ($product) {
            if ($product->has_tva) {
                $tva = CompanySetting::first()?->tva_rate ?? 18;
                $product->selling_price_ttc = $product->selling_price * (1 + $tva / 100);
            } else {
                $product->selling_price_ttc = $product->selling_price;
            }
        });
    }

    // Relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    // Helpers
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->stock_alert_threshold;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    public function isOnSale(): bool
    {
        return $this->old_price !== null && $this->old_price > $this->selling_price;
    }

    public function discountPercent(): int
    {
        if (!$this->isOnSale()) return 0;
        return (int) round((($this->old_price - $this->selling_price) / $this->old_price) * 100);
    }

    public function reviews()
{
    return $this->hasMany(ProductReview::class);
}

    public function approvedReviews()
    {
        return $this->hasMany(ProductReview::class)
            ->where('is_approved', true)
            ->latest();
    }

    public function averageRating(): float
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }
}
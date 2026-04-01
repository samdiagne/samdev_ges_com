<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'is_active'
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    // Auto-générer le slug
    protected static function booted(): void
    {
        static::creating(function ($category) {
            $category->slug = self::generateUniqueSlug($category->name);
        });

        static::updating(function ($category) {
            $category->slug = self::generateUniqueSlug($category->name, $category->id);
        });
    }

    protected static function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (
            static::withTrashed()
                ->where('slug', $slug)
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function activeProducts()
    {
        return $this->hasMany(Product::class)
        ->where('is_active', true);
    }
}
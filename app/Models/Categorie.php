<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Categorie extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'image',
        'ordre',
        'est_active',
    ];

    protected function casts(): array
    {
        return [
            'est_active' => 'boolean',
            'ordre'      => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Categorie $categorie) {
            if (empty($categorie->slug)) {
                $categorie->slug = Str::slug($categorie->nom);
            }
        });

        static::updating(function (Categorie $categorie) {
            if ($categorie->isDirty('nom') && empty($categorie->slug)) {
                $categorie->slug = Str::slug($categorie->nom);
            }
        });
    }

    // ── Relations ─────────────────────────────────

    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class, 'categorie_id');
    }

    public function produitsActifs(): HasMany
    {
        return $this->hasMany(Produit::class, 'categorie_id')
            ->where('est_actif', true);
    }
}

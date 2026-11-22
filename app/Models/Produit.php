<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Produit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produits';

    protected $fillable = [
        'categorie_id',
        'nom',
        'slug',
        'description',
        'description_courte',
        'ingredients',
        'prix_achat',
        'prix_vente',
        'ancien_prix',
        'quantite_palier',
        'prix_palier',
        'stock',
        'seuil_alerte_stock',
        'sur_commande',
        'est_actif',
        'est_mis_en_avant',
        'est_variante',
        'est_menu',
        'mode_prix_menu',
        'supplement_menu',
        'image_principale',
    ];

    protected function casts(): array
    {
        return [
            'ingredients'       => 'array',
            'prix_achat'        => 'decimal:2',
            'prix_vente'        => 'decimal:2',
            'ancien_prix'       => 'decimal:2',
            'quantite_palier'   => 'integer',
            'prix_palier'       => 'decimal:2',
            'sur_commande'      => 'boolean',
            'est_actif'         => 'boolean',
            'est_mis_en_avant'  => 'boolean',
            'est_variante'      => 'boolean',
            'est_menu'          => 'boolean',
            'supplement_menu'   => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Produit $produit) {
            $produit->slug = Str::slug($produit->nom);
        });
    }

    // ── Relations ─────────────────────────────────

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function composants(): HasMany
    {
        return $this->hasMany(MenuComposant::class, 'produit_id')->orderBy('ordre');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProduitImage::class, 'produit_id')->orderBy('ordre');
    }

    public function avis(): HasMany
    {
        return $this->hasMany(Avis::class, 'produit_id');
    }

    public function avisApprouves(): HasMany
    {
        return $this->hasMany(Avis::class, 'produit_id')
            ->where('est_approuve', true)
            ->latest();
    }

    public function mouvementsStock(): HasMany
    {
        return $this->hasMany(MouvementStock::class, 'produit_id');
    }

    // ── Helpers ───────────────────────────────────

    public function estEnRuptureDeStock(): bool
    {
        if ($this->sur_commande) return false;
        return $this->stock <= 0;
    }

    public function estStockFaible(): bool
    {
        if ($this->sur_commande) return false;
        return $this->stock > 0 && $this->stock <= $this->seuil_alerte_stock;
    }

    public function estEnPromotion(): bool
    {
        return $this->ancien_prix !== null && $this->ancien_prix > $this->prix_vente;
    }

    public function pourcentageRemise(): int
    {
        if (! $this->estEnPromotion()) return 0;
        return (int) round((($this->ancien_prix - $this->prix_vente) / $this->ancien_prix) * 100);
    }

    public function noteMoyenne(): float
    {
        return round($this->avisApprouves()->avg('note') ?? 0, 1);
    }

    public function prixPourQuantite(int $quantite): float
    {
        if ($this->quantite_palier && $this->prix_palier && $quantite >= $this->quantite_palier) {
            return (float) $this->prix_palier;
        }

        return (float) $this->prix_vente;
    }

    public function isMenu(): bool
    {
        if ($this->est_menu) return true;
        if ($this->relationLoaded('composants') ? $this->composants->isNotEmpty() : $this->composants()->exists()) return true;
        $catNom = strtolower($this->categorie?->nom ?? '');
        $catSlug = strtolower($this->categorie?->slug ?? '');
        return str_contains($catNom, 'menu') || str_contains($catSlug, 'menu');
    }

    public function peutAvoirSupplements(): bool
    {
        // Produits éligibles aux suppléments (Burgers, Tacos, Sandwichs, Wraps, Chawarmas, Menus)
        if (in_array((int) $this->categorie_id, [20, 21, 22, 23, 25])) {
            return true;
        }

        $catSlug = $this->categorie?->slug ?? '';
        $catNom  = strtolower($this->categorie?->nom ?? '');
        $prodNom = strtolower($this->nom);

        foreach (['burger', 'tacos', 'sandwich', 'wrap', 'chawarma', 'menu'] as $keyword) {
            if (str_contains($catSlug, $keyword) || str_contains($catNom, $keyword) || str_contains($prodNom, $keyword)) {
                return true;
            }
        }

        return false;
    }
}

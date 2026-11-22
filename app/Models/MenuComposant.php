<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuComposant extends Model
{
    protected $table = 'menu_composants';

    protected $fillable = [
        'produit_id',
        'titre',
        'type_selection',
        'categorie_id',
        'produit_fixe_id',
        'ordre',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function produitFixe(): BelongsTo
    {
        return $this->belongsTo(Produit::class, 'produit_fixe_id');
    }

    /**
     * Retourne la liste des produits disponibles pour cette étape.
     */
    public function getOptionsProduits()
    {
        if ($this->type_selection === 'produit_fixe' && $this->produitFixe) {
            return collect([$this->produitFixe]);
        }

        if ($this->categorie_id) {
            return Produit::where('categorie_id', $this->categorie_id)
                ->where('est_actif', true)
                ->orderBy('nom')
                ->get();
        }

        return collect();
    }
}

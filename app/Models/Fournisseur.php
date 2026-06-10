<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fournisseur extends Model
{
    use SoftDeletes;

    protected $table = 'fournisseurs';

    protected $fillable = [
        'nom',
        'telephone',
        'email',
        'adresse',
        'notes',
        'est_actif',
    ];

    protected function casts(): array
    {
        return [
            'est_actif' => 'boolean',
        ];
    }

    // ── Relations ─────────────────────────────────

    public function achats(): HasMany
    {
        return $this->hasMany(Achat::class, 'fournisseur_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commande extends Model
{
    use SoftDeletes;

    protected $table = 'commandes';

    protected $fillable = [
        'reference',
        'user_id',
        'vente_id',
        'nom_client',
        'email_client',
        'telephone_client',
        'adresse_livraison',
        'ville',
        'notes',
        'sous_total',
        'frais_livraison',
        'total',
        'mode_paiement',
        'statut_paiement',
        'paydunya_token',
        'paydunya_invoice_token',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'sous_total'      => 'decimal:2',
            'frais_livraison' => 'decimal:2',
            'total'           => 'decimal:2',
        ];
    }

    // ── Relations ─────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vente(): BelongsTo
    {
        return $this->belongsTo(Vente::class, 'vente_id');
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(CommandeLigne::class, 'commande_id');
    }

    // ── Helpers ───────────────────────────────────

    public function estPayee(): bool
    {
        return $this->statut_paiement === 'paye';
    }

    public function estAnnulee(): bool
    {
        return $this->statut === 'annulee';
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'en_attente' => 'En attente',
            'confirmee'  => 'Confirmée',
            'expediee'   => 'Expédiée',
            'livree'     => 'Livrée',
            'annulee'    => 'Annulée',
            default      => ucfirst($this->statut ?? '—'),
        };
    }

    public function getStatutColorAttribute(): string
    {
        return match ($this->statut) {
            'en_attente' => 'bg-yellow-100 text-yellow-700',
            'confirmee'  => 'bg-blue-100 text-blue-700',
            'expediee'   => 'bg-purple-100 text-purple-700',
            'livree'     => 'bg-green-100 text-green-700',
            'annulee'    => 'bg-red-100 text-red-600',
            default      => 'bg-gray-100 text-gray-600',
        };
    }

    public function getPaiementLabelAttribute(): string
    {
        return match ($this->mode_paiement) {
            'cash'     => 'Espèces',
            'wave'     => 'Wave',
            'om'       => 'Orange Money',
            'carte'    => 'Carte bancaire',
            'paydunya' => 'PayDunya',
            default    => ucfirst($this->mode_paiement ?? '—'),
        };
    }
}

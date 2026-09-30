<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facture extends Model
{
    protected $table = 'factures';

    protected $fillable = [
        'vente_id',
        'client_id',
        'numero_facture',
        'statut',
        'total',
        'chemin_pdf',
        'envoye_whatsapp',
        'envoye_email',
        'emise_le',
    ];

    protected function casts(): array
    {
        return [
            'total'           => 'decimal:2',
            'envoye_whatsapp' => 'boolean',
            'envoye_email'    => 'boolean',
            'emise_le'        => 'datetime',
        ];
    }

    // ── Statuts ───────────────────────────────────
    public const STATUT_PAYEE    = 'payee';
    public const STATUT_A_REGLER = 'a_regler';

    public function estPayee(): bool
    {
        return $this->statut === self::STATUT_PAYEE;
    }

    public function estARegler(): bool
    {
        return $this->statut === self::STATUT_A_REGLER || $this->statut === 'emise';
    }

    // ── Relations ─────────────────────────────────

    public function vente(): BelongsTo
    {
        return $this->belongsTo(Vente::class, 'vente_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}

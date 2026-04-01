<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'ninea',
    'rc',
    'phone',
    'whatsapp',
    'email',
    'address',
    'facebook',
    'instagram',
    'tiktok',
    'logo',
    'tva_rate',
    'currency',
    'invoice_prefix',
    'invoice_counter',
    'stamp_image',
    'signature_image',
    ];

    protected function casts(): array
    {
        return ['tva_rate' => 'decimal:2'];
    }

    // Helper statique pour récupérer les settings facilement
    public static function getSettings(): self
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'name'            => 'Al-Adab Tech Store',
                'tva_rate'        => 18.00,
                'currency'        => 'FCFA',
                'invoice_prefix'  => 'FAC',
                'invoice_counter' => 1,
            ]
        );
    }
}
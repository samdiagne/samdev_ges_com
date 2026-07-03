<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    protected $table = 'parametres';

    protected $fillable = [
        'nom',
        'ninea',
        'rc',
        'telephone',
        'whatsapp',
        'email',
        'adresse',
        'logo',
        'image_hero',
        'signature',
        'cachet',
        'devise',
        'prefixe_facture',
        'compteur_facture',
        'facebook',
        'instagram',
        'tiktok',
        'lien_whatsapp',
    ];
}

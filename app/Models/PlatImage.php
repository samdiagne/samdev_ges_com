<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatImage extends Model
{
    protected $table = 'plat_images';

    protected $fillable = [
        'plat_id',
        'chemin',
        'ordre',
    ];

    public function plat(): BelongsTo
    {
        return $this->belongsTo(Plat::class, 'plat_id');
    }
}

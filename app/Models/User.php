<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'whatsapp',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isGestionnaire(): bool
    {
        return $this->hasRole(['admin', 'gestionnaire']);
    }

    public function isClient(): bool
    {
        return $this->hasRole('client');
    }

    // ── Relations ─────────────────────────────────

    public function client(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function ventes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Vente::class);
    }

    public function achats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Achat::class);
    }

    public function depenses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Depense::class);
    }

    public function commandes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Commande::class);
    }
}

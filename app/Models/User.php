<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


/**
 * @method bool hasRole(string|array $roles)
 * @method bool hasAnyRole(string|array $roles)
 * @method bool hasAllRoles(string|array $roles)
 * @method \Illuminate\Support\Collection getRoleNames()
 * @method $this assignRole(string|array $roles)
 * @method $this removeRole(string $role)
 * @method bool hasPermissionTo(string $permission)
 */
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

    public function customer(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\Customer::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isGestionnaire(): bool
    {
        return $this->hasRole('gestionnaire');
    }

    public function isClient(): bool
    {
        return $this->hasRole('client');
    }
}
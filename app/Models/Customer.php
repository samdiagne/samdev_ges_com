<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'whatsapp',
        'email',
        'address',
        'type',
        'company_name',
        'ninea',
        'total_purchases',
        'notes',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_phone', 'phone');
    }

    protected function casts(): array
    {
        return [
            'total_purchases' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
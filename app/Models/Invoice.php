<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id', 'customer_id', 'invoice_number', 'status',
        'subtotal_ht', 'tva_rate', 'tva_amount', 'total_ttc',
        'pdf_path', 'sent_whatsapp', 'sent_email', 'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal_ht'   => 'decimal:2',
            'tva_rate'      => 'decimal:2',
            'tva_amount'    => 'decimal:2',
            'total_ttc'     => 'decimal:2',
            'sent_whatsapp' => 'boolean',
            'sent_email'    => 'boolean',
            'issued_at'     => 'datetime',
        ];
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
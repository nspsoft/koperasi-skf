<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditInstallment extends Model
{
    protected $fillable = [
        'transaction_id',
        'installment_number',
        'due_date',
        'amount',
        'status',
        'paid_at',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}


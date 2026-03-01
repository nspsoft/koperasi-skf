<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_number', 'user_id', 'cashier_id', 'type', 'status', 
        'payment_method', 'total_amount', 'paid_amount', 
        'change_amount', 'notes', 'credit_tenor_months', 'credit_installment_amount'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'credit_installment_amount' => 'decimal:2',
        'credit_tenor_months' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function creditInstallments()
    {
        return $this->hasMany(CreditInstallment::class);
    }

    public function journalEntry()
    {
        return $this->morphOne(JournalEntry::class, 'reference');
    }
}

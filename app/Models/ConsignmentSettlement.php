<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsignmentSettlement extends Model
{
    protected $fillable = [
        'transaction_number',
        'consignor_type',
        'consignor_id',
        'period_start',
        'period_end',
        'total_sales_amount',
        'total_payable_amount',
        'total_profit_amount',
        'status',
        'paid_at',
        'paid_by',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'paid_at' => 'datetime',
        'total_sales_amount' => 'decimal:2',
        'total_payable_amount' => 'decimal:2',
        'total_profit_amount' => 'decimal:2',
    ];

    public function consignor()
    {
        return $this->morphTo();
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->transaction_number = 'CS-' . date('Ymd') . '-' . mt_rand(1000, 9999);
        });
    }
}

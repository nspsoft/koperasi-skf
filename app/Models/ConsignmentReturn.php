<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsignmentReturn extends Model
{
    protected $fillable = [
        'transaction_number',
        'return_date',
        'consignor_type',
        'consignor_id',
        'total_items',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'return_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(ConsignmentReturnItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function consignor()
    {
        return $this->morphTo(__FUNCTION__, 'consignor_type', 'consignor_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($return) {
            if (empty($return->transaction_number)) {
                $date = now()->format('Ymd');
                $last = self::whereDate('created_at', now()->toDateString())->count();
                $return->transaction_number = 'RET-CON-' . $date . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}

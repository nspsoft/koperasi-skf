<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsignmentInbound extends Model
{
    protected $fillable = [
        'transaction_number',
        'consignor_type',
        'consignor_id',
        'inbound_date',
        'status',
        'note',
        'created_by'
    ];

    protected $casts = [
        'inbound_date' => 'date',
    ];

    public function consignor()
    {
        return $this->morphTo();
    }

    public function items()
    {
        return $this->hasMany(ConsignmentInboundItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->transaction_number = 'CIN-' . date('Ymd') . '-' . mt_rand(1000, 9999);
            if (!$model->created_by) {
                $model->created_by = auth()->id();
            }
        });
    }
}

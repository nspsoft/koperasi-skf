<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsignmentReturnItem extends Model
{
    protected $fillable = [
        'consignment_return_id',
        'product_id',
        'quantity',
        'notes',
    ];

    public function consignmentReturn()
    {
        return $this->belongsTo(ConsignmentReturn::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

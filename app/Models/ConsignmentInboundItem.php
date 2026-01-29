<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsignmentInboundItem extends Model
{
    protected $fillable = [
        'consignment_inbound_id',
        'product_id',
        'quantity',
        'unit_cost'
    ];

    public function inbound()
    {
        return $this->belongsTo(ConsignmentInbound::class, 'consignment_inbound_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

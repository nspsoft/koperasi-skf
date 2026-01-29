<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_purchase', 
        'usage_limit', 'used_count', 'start_date', 
        'end_date', 'is_active'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValidFor($amount)
    {
        if (!$this->is_active) return false;
        
        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) return false;
        if ($this->end_date && $now->gt($this->end_date)) return false;
        
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) return false;
        
        if ($amount < $this->min_purchase) return false;

        return true;
    }

    public function calculateDiscount($amount)
    {
        if ($this->type === 'percentage') {
            return ($this->value / 100) * $amount;
        }
        return min($this->value, $amount);
    }}

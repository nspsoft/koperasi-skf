<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'account_category_id', 'code', 'name', 'type', 'sub_type',
        'normal_balance', 'is_active', 'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(AccountCategory::class, 'account_category_id');
    }

    /**
     * Get type label in Indonesian
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'asset' => 'Aktiva',
            'liability' => 'Kewajiban',
            'equity' => 'Modal',
            'revenue' => 'Pendapatan',
            'expense' => 'Biaya',
            default => $this->type,
        };
    }

    /**
     * Get normal balance label
     */
    public function getNormalBalanceLabelAttribute()
    {
        return match($this->normal_balance) {
            'debit' => 'Debit',
            'credit' => 'Kredit',
            default => $this->normal_balance,
        };
    }

    /**
     * Scope: Only active accounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountCategory extends Model
{
    protected $fillable = [
        'code', 'name', 'type', 'description'
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class);
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
}

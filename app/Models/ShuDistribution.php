<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShuDistribution extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'period_year',
        'member_id',
        'total_savings',
        'total_transactions',
        'total_loans',
        'shu_savings',
        'shu_transactions',
        'shu_jasa',
        'total_shu',
        'status',
        'distributed_at',
        'calculated_by'
    ];

    protected $casts = [
        'total_savings' => 'decimal:2',
        'total_transactions' => 'decimal:2',
        'total_loans' => 'decimal:2',
        'shu_savings' => 'decimal:2',
        'shu_transactions' => 'decimal:2',
        'shu_jasa' => 'decimal:2',
        'total_shu' => 'decimal:2',
        'distributed_at' => 'datetime',
    ];

    /**
     * Member who receives this SHU
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Admin who calculated this SHU
     */
    public function calculator()
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    /**
     * Status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'calculated' => 'warning',
            'distributed' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'calculated' => 'Terhitung',
            'distributed' => 'Dibagikan',
            default => $this->status,
        };
    }
}

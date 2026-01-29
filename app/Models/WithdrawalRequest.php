<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawalRequest extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'member_id',
        'amount',
        'saving_type',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'status',
        'reason',
        'admin_notes',
        'approved_by',
        'approved_at',
        'completed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Member who made the request
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Admin who approved/rejected the request
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'completed' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Status label in Indonesian
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'completed' => 'Selesai',
            default => $this->status,
        };
    }

    /**
     * Saving type label
     */
    public function getSavingTypeLabelAttribute()
    {
        return match($this->saving_type) {
            'pokok' => 'Simpanan Pokok',
            'wajib' => 'Simpanan Wajib',
            'sukarela' => 'Simpanan Sukarela',
            default => $this->saving_type,
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceHistory extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'points_change',
        'type',
        'reason',
        'admin_id'
    ];

    /**
     * User who received the points
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Admin who gave/deducted the points
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

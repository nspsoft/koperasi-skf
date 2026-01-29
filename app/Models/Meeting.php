<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'scheduled_at',
        'location',
        'agenda',
        'notes',
        'status',
        'attendance_list',
        'attachment_path',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'attendance_list' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

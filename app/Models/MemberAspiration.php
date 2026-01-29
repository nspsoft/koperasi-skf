<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberAspiration extends Model
{
    protected $fillable = [
        'member_id',
        'type',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }}

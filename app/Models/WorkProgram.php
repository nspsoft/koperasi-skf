<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkProgram extends Model
{
    protected $guarded = ['id'];
    
    protected $fillable = [
        'title',
        'description',
        'icon',
        'color',
        'order'
    ];

}

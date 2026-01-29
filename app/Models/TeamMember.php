<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $guarded = ['id'];
    
    protected $fillable = [
        'name',
        'role',
        'image',
        'bio',
        'twitter_link',
        'facebook_link',
        'instagram_link',
        'linkedin_link',
        'order',
    ];
}

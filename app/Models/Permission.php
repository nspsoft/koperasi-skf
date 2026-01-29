<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'label',
        'group',
    ];

    /**
     * Get roles that have this permission
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    /**
     * Get all permissions grouped by their group
     */
    public static function allGrouped(): array
    {
        return static::orderBy('group')->orderBy('label')->get()->groupBy('group')->toArray();
    }
}

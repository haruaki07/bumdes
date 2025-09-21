<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'description',
        'menu',
        'menu_group',
        'guard_name',
    ];

    public function scopeGuardName($query, $guardName)
    {
        return $query->where('guard_name', $guardName);
    }
}

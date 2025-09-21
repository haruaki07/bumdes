<?php

namespace App\Models;

use App\Traits\Datatable;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use Datatable;

    protected $fillable = [
        'name',
        'description',
        'guard_name',
    ];

    protected $dataTableColumns = [
        'name' => 'searchable|sortable',
        'description' => 'searchable|sortable',
        'created_at' => 'sortable',
        'updated_at' => 'sortable',
    ];

    public function scopeGuardName($query, $guardName)
    {
        return $query->where('guard_name', $guardName);
    }
}

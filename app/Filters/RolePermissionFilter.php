<?php

namespace App\Filters;

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use TakiElias\Tablar\Menu\Filters\FilterInterface;

class RolePermissionFilter implements FilterInterface
{
    public function transform($item)
    {
        if (! $this->isVisible($item)) {
            return false;
        }

        return $item['header'] ?? $item;
    }

    protected function isVisible($item)
    {
        /** @var \Modules\EBilling\Models\User|\App\Models\User */
        $user = ($item['group'] ?? null) === 'e-billing' ? Auth::guard('ebil')->user() : Auth::user();

        // If user model does not use HasRoles trait, bypass the filter
        if (! in_array(HasRoles::class, class_uses_recursive($user))) {
            return true;
        }

        if ($user->hasRole('admin')) {
            return true;
        }

        $hasAnyRole = $item['hasAnyRole'] ?? null;
        $hasRole = $item['hasRole'] ?? null;

        if (($hasAnyRole && $user->hasAnyRole($hasAnyRole)) || ($hasRole && $user->hasRole($hasRole))) {
            return true;
        }

        return $this->checkPermissions($item, $user) ?? true;
    }

    protected function checkPermissions($item, $user)
    {
        $hasAnyPermission = $item['hasAnyPermission'] ?? null;

        return $hasAnyPermission ? $user->hasAnyPermission($hasAnyPermission) : null;
    }
}

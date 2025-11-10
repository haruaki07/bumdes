<?php

namespace App\Filters;

use Illuminate\Support\Facades\Auth;

class UserRoleFilter
{
    public function transform($item)
    {
        if (isset($item['role']) && Auth::check()) {
            $userRole = Auth::user()->role;
            if (in_array($userRole, (array) $item['role'])) {
                return $item;
            }

            return false;
        }

        return $item;
    }
}

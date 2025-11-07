<?php

namespace App\Policies;

use App\Models\BusinessType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BusinessTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {
        return true; // Everyone can view the list
    }

    public function view(?User $user, BusinessType $businessType): bool
    {
        return true; // Everyone can view details
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'operator';
    }

    public function update(User $user, BusinessType $businessType): bool
    {
        return $user->role === 'admin' || $user->role === 'operator';
    }

    public function delete(User $user, BusinessType $businessType): bool
    {
        return $user->role === 'admin' || $user->role === 'operator';
    }
}

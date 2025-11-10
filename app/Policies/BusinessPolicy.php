<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BusinessPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {
        return true; // Everyone can view the list
    }

    public function view(?User $user, Business $business): bool
    {
        if (! $user) {
            return false;
        }

        return $user->role === 'admin' ||
            $user->role === 'operator' ||
            $business->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'operator';
    }

    public function update(User $user, Business $business): bool
    {
        return $user->role === 'admin' || $user->role === 'operator';
    }

    public function delete(User $user, Business $business): bool
    {
        return $user->role === 'admin' || $user->role === 'operator';
    }

    public function requestNameChange(User $user, Business $business): bool
    {
        return $business->owner_id === $user->id && ! $business->hasNameChangeRequest();
    }

    public function approveNameChange(User $user, Business $business): bool
    {
        return ($user->role === 'admin' || $user->role === 'operator') && $business->hasNameChangeRequest();
    }

    public function rejectNameChange(User $user, Business $business): bool
    {
        return ($user->role === 'admin' || $user->role === 'operator') && $business->hasNameChangeRequest();
    }
}

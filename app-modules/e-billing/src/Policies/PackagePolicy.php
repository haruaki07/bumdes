<?php

namespace Modules\EBilling\Policies;

use Modules\EBilling\Models\Package;
use Modules\EBilling\Models\User;

class PackagePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('read-packages');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Package $package): bool
    {
        return $user->hasPermissionTo('read-packages');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-packages');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Package $package): bool
    {
        return $user->hasPermissionTo('update-packages');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Package $package): bool
    {
        return $user->hasPermissionTo('delete-packages');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Package $package): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Package $package): bool
    {
        return false;
    }
}

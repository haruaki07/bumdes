<?php

namespace App\Policies;

use App\Models\BusinessRegistration;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BusinessRegistrationPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(?User $user): bool
    {
        return true; // Everyone can view the list
    }

    public function view(?User $user, BusinessRegistration $businessRegistration): bool
    {
        if (! $user) {
            return false;
        }

        return $user->role === 'admin' ||
            $user->role === 'petugas' ||
            $businessRegistration->applicant_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'warga';
    }

    public function update(User $user, BusinessRegistration $businessRegistration): bool
    {
        if ($user->role === 'admin' || $user->role === 'petugas') {
            return true;
        }

        return $businessRegistration->applicant_id === $user->id &&
            $businessRegistration->status === 'pending';
    }

    public function delete(User $user, BusinessRegistration $businessRegistration): bool
    {
        if ($user->role === 'admin' || $user->role === 'petugas') {
            return true;
        }

        return $businessRegistration->applicant_id === $user->id &&
            $businessRegistration->status === 'pending' &&
            ! $businessRegistration->is_revised;
    }

    public function approve(User $user, BusinessRegistration $businessRegistration): bool
    {
        return $user->role === 'admin' || $user->role === 'petugas';
    }

    public function reject(User $user, BusinessRegistration $businessRegistration): bool
    {
        return $user->role === 'admin' || $user->role === 'petugas';
    }

    public function revise(User $user, BusinessRegistration $businessRegistration): bool
    {
        if ($user->role === 'admin' || $user->role === 'petugas') {
            return true;
        }

        return $businessRegistration->applicant_id === $user->id &&
            $businessRegistration->canBeRevised();
    }
}

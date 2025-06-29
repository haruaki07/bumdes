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
        if (!$user) {
            return false;
        }

        return $user->hasRole('admin') ||
            $user->hasRole('petugas') ||
            $businessRegistration->applicant_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('warga');
    }

    public function update(User $user, BusinessRegistration $businessRegistration): bool
    {
        if ($user->hasRole('admin') || $user->hasRole('petugas')) {
            return true;
        }

        return $businessRegistration->applicant_id === $user->id &&
            $businessRegistration->status === 'pending';
    }

    public function delete(User $user, BusinessRegistration $businessRegistration): bool
    {
        if ($user->hasRole('admin') || $user->hasRole('petugas')) {
            return true;
        }

        return $businessRegistration->applicant_id === $user->id &&
            $businessRegistration->status === 'pending' &&
            !$businessRegistration->is_revised;
    }

    public function approve(User $user, BusinessRegistration $businessRegistration): bool
    {
        return $user->hasRole('admin') || $user->hasRole('petugas');
    }

    public function reject(User $user, BusinessRegistration $businessRegistration): bool
    {
        return $user->hasRole('admin') || $user->hasRole('petugas');
    }

    public function revise(User $user, BusinessRegistration $businessRegistration): bool
    {
        if ($user->hasRole('admin') || $user->hasRole('petugas')) {
            return true;
        }

        return $businessRegistration->applicant_id === $user->id &&
            $businessRegistration->canBeRevised();
    }
}

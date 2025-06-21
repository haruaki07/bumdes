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
    if (!$user) {
      return false;
    }

    return $user->hasRole('admin') ||
      $user->hasRole('petugas') ||
      $business->owner_id === $user->id;
  }

  public function create(User $user): bool
  {
    return $user->hasRole('warga') ||
      $user->hasRole('admin') ||
      $user->hasRole('petugas');
  }

  public function update(User $user, Business $business): bool
  {
    if ($user->hasRole('admin') || $user->hasRole('petugas')) {
      return true;
    }

    return $business->owner_id === $user->id &&
      $business->status === 'pending';
  }

  public function delete(User $user, Business $business): bool
  {
    return $user->hasRole('admin') ||
      ($business->owner_id === $user->id &&
        $business->status === 'pending');
  }

  public function approve(User $user, Business $business): bool
  {
    return $user->hasRole('admin') || $user->hasRole('petugas');
  }

  public function reject(User $user, Business $business): bool
  {
    return $user->hasRole('admin') || $user->hasRole('petugas');
  }
}

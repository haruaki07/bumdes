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
    return $user->hasRole('admin') || $user->hasRole('petugas');
  }

  public function update(User $user, Business $business): bool
  {
    return $user->hasRole('admin') || $user->hasRole('petugas');
  }

  public function delete(User $user, Business $business): bool
  {
    return $user->hasRole('admin') || $user->hasRole('petugas');
  }

  public function requestNameChange(User $user, Business $business): bool
  {
    return $business->owner_id === $user->id && !$business->hasNameChangeRequest();
  }

  public function approveNameChange(User $user, Business $business): bool
  {
    return ($user->hasRole('admin') || $user->hasRole('petugas')) && $business->hasNameChangeRequest();
  }

  public function rejectNameChange(User $user, Business $business): bool
  {
    return ($user->hasRole('admin') || $user->hasRole('petugas')) && $business->hasNameChangeRequest();
  }
}

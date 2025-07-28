<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Anyone can view the list of users
    }

    /**
     * Determine whether the user can view the user.
     */
    public function view(?User $user, User $model): bool
    {
        return true; // Anyone can view a user
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can update the user.
     */
    public function update(User $user, User $model): bool
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can soft-delete the user.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can restore the user.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can permanently delete the soft-deleted user.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can view soft-deleted users.
     */
    public function viewTrash(User $user): bool
    {
        return $user->can('manage users');
    }

    /**
     * Determine whether the user can permanently delete all soft-deleted users.
     */
    public function emptyTrash(User $user): bool
    {
        return $user->can('manage users');
    }
}

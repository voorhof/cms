<?php

namespace App\Services\Cms;

use App\Models\User;
use App\Services\Cms\Contracts\UserServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Voorhof\Flash\Facades\Flash;

class UserService implements UserServiceInterface
{
    /**
     * Create a new user with the given data
     *
     * @param  array  $userData  Validated user data
     * @param  string|null  $role  Role to assign to the user
     * @return User The created user
     */
    public function createUser(array $userData, ?string $role = null): User
    {
        // Create a new user
        $user = new User($userData);

        // Create a random password
        $user->password = Hash::make(str()->random(12));

        // Save user
        $user->save();

        // Assign a role to user if current user can manage roles
        if (Auth::user()->can('manage roles')) {
            $user->syncRoles([$role]);
        }

        // Flash message:
        Flash::success(__('Successful creation!'));

        // Return user
        return $user;
    }

    /**
     * Update an existing user with the given data
     *
     * @param  User  $user  The user to update
     * @param  array  $userData  Validated user data
     * @param  string|null  $role  Role to assign to the user
     * @return User The updated user
     */
    public function updateUser(User $user, array $userData, ?string $role = null): User
    {
        // Only update the user when it does not have the super-admin role (or when the auth user is a super-admin)
        if (! $user->hasRole('super-admin') || Auth::user()->hasRole('super-admin')) {
            $user->fill($userData);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            // Assign a role to user if current user can manage roles and the role is not secured
            if (Auth::user()->can('manage roles') && ! $user->hasRole(config('cms.secured_roles'))) {
                $user->syncRoles([$role]);
            }

            Flash::success(__('Successful update!'));
        } else {
            Flash::danger(__('Unable to update!'));
        }

        // Return user
        return $user;
    }

    /**
     * Delete a user (soft delete)
     *
     * @param  User  $user  The user to delete
     * @return bool Whether the deletion was successful
     */
    public function deleteUser(User $user): bool
    {
        // only delete the user when it does not have the super-admin role (or when the auth user is a super-admin)
        if (! $user->hasRole('super-admin') || Auth::user()->hasRole('super-admin')) {
            $user->email_verified_at = null;
            $user->save();
            $user->delete();

            Flash::warning(__('Successful delete!'));

            return true;
        } else {
            Flash::danger(__('Unable to delete!'));

            return false;
        }
    }

    /**
     * Restore a soft-deleted user
     *
     * @param  User  $user  The user to restore
     * @return User The restored user
     */
    public function restoreUser(User $user): User
    {
        $user->restore();

        Flash::success(__('Successful restore!'));

        return $user;
    }

    /**
     * Permanently delete a user
     *
     * @param  User  $user  The user to permanently delete
     */
    public function forceDeleteUser(User $user): void
    {
        $user->forceDelete();

        Flash::warning(__('Successful delete!'));
    }

    /**
     * Permanently delete all soft-deleted users
     */
    public function emptyTrash(): void
    {
        foreach (User::onlyTrashed()->get() as $user) {
            $user->forceDelete();
        }

        Flash::warning(__('Successful delete!'));
    }
}

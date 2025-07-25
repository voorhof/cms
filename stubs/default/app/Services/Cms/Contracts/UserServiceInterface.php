<?php

namespace App\Services\Cms\Contracts;

use App\Models\User;

interface UserServiceInterface
{
    /**
     * Create a new user with the given data
     *
     * @param  array  $userData  Validated user data
     * @param  string|null  $role  Role to assign to the user
     * @return User The created user
     */
    public function createUser(array $userData, ?string $role = null): User;

    /**
     * Update an existing user with the given data
     *
     * @param  User  $user  The user to update
     * @param  array  $userData  Validated user data
     * @param  string|null  $role  Role to assign to the user
     * @return User The updated user
     */
    public function updateUser(User $user, array $userData, ?string $role = null): User;

    /**
     * Delete a user (soft delete)
     *
     * @param  User  $user  The user to delete
     * @return bool Whether the deletion was successful
     */
    public function deleteUser(User $user): bool;

    /**
     * Restore a soft-deleted user
     *
     * @param  User  $user  The user to restore
     * @return User The restored user
     */
    public function restoreUser(User $user): User;

    /**
     * Permanently delete a user
     *
     * @param  User  $user  The user to permanently delete
     */
    public function forceDeleteUser(User $user): void;

    /**
     * Permanently delete all soft-deleted users
     */
    public function emptyTrash(): void;
}

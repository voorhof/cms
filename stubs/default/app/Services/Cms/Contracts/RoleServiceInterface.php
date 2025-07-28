<?php

namespace App\Services\Cms\Contracts;

use App\Models\Role;

interface RoleServiceInterface
{
    /**
     * Create a new role with the given data
     *
     * @param  string  $name  Role name
     * @param  array  $permissions  Permissions to assign to the role
     * @return Role The created role
     */
    public function createRole(string $name, array $permissions = []): Role;

    /**
     * Update an existing role with the given data
     *
     * @param  Role  $role  The role to update
     * @param  string  $name  New role name
     * @param  array  $permissions  Permissions to assign to the role
     * @return Role The updated role
     */
    public function updateRole(Role $role, string $name, array $permissions = []): Role;

    /**
     * Delete a role
     *
     * @param  Role  $role  The role to permanently delete
     */
    public function deleteRole(Role $role): void;
}

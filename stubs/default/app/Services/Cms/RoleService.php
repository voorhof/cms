<?php

namespace App\Services\Cms;

use App\Services\Cms\Contracts\RoleServiceInterface;
use Spatie\Permission\Models\Role;
use Voorhof\Flash\Facades\Flash;

class RoleService implements RoleServiceInterface
{
    /**
     * Create a new role with the given data
     *
     * @param  string  $name  Role name
     * @param  array  $permissions  Permissions to assign to the role
     * @return Role The created role
     */
    public function createRole(string $name, array $permissions = []): Role
    {
        // Create a new role
        $role = Role::create(['name' => $name]);

        // Assign permissions to the role
        if (! empty($permissions)) {
            $role->syncPermissions($permissions);
        }

        // Flash message
        Flash::success(__('Successful creation!'));

        // Return role
        return $role;
    }

    /**
     * Update an existing role with the given data
     *
     * @param  Role  $role  The role to update
     * @param  string  $name  New role name
     * @param  array  $permissions  Permissions to assign to the role
     * @return Role The updated role
     */
    public function updateRole(Role $role, string $name, array $permissions = []): Role
    {
        // Only update the role when it is not a secured role
        if (! in_array($role->name, config('cms.secured_roles'))) {
            // Update role name
            $role->name = $name;
            $role->save();

            // Sync permissions
            $role->syncPermissions($permissions);

            Flash::success(__('Successful update!'));
        } else {
            Flash::danger(__('Unable to update!'));
        }

        // Return role
        return $role;
    }

    /**
     * Delete a role
     *
     * @param  Role  $role  The role to delete
     * @return bool Whether the deletion was successful
     */
    public function deleteRole(Role $role): bool
    {
        // Only delete the role when it is not a secured role
        if (! in_array($role->name, config('cms.secured_roles'))) {
            $role->delete();

            Flash::warning(__('Successful delete!'));

            return true;
        } else {
            Flash::danger(__('Unable to delete!'));

            return false;
        }
    }
}

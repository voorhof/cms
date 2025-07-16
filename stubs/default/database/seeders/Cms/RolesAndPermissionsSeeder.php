<?php

namespace Database\Seeders\Cms;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        Permission::create(['name' => 'access cms']);
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'manage roles']);
        Permission::create(['name' => 'manage posts']);
        Permission::create(['name' => 'create post']);
        Permission::create(['name' => 'edit post']);
        Permission::create(['name' => 'publish post']);

        // Create Roles & assign Permissions
        // The super-admin permissions are handled by a global Gate within the boot method of the AppServiceProvider
        Role::create(['name' => 'subscriber']);
        Role::create(['name' => 'contributor'])->givePermissionTo('access cms', 'create post', 'edit post');
        Role::create(['name' => 'editor'])->givePermissionTo('access cms', 'manage users', 'create post', 'edit post', 'publish post');
        Role::create(['name' => 'manager'])->givePermissionTo('access cms', 'manage users', 'manage roles', 'manage posts');
        Role::create(['name' => 'admin'])->givePermissionTo(Permission::all());
        Role::create(['name' => 'super-admin']);
    }
}

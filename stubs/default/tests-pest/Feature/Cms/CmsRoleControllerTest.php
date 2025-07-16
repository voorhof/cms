<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Create necessary permissions
    Permission::create(['name' => 'access cms']);
    Permission::create(['name' => 'manage roles']);

    // Create roles
    Role::create(['name' => 'super-admin']);
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'editor']);

    // Create a user with permission to manage roles
    $this->userWithPermission = User::factory()->create(['name' => 'Admin User']);
    $this->userWithPermission->givePermissionTo(['access cms', 'manage roles']);

    // Create a user without permission to manage roles
    $this->userWithoutPermission = User::factory()->create(['name' => 'Regular User']);
    $this->userWithoutPermission->givePermissionTo('access cms');

    // Create a super-admin user
    $this->superAdmin = User::factory()->create(['name' => 'Super Admin']);
    $this->superAdmin->assignRole('super-admin');
    $this->superAdmin->givePermissionTo(['access cms', 'manage roles']);

    // Create a test role that will be manipulated in tests
    $this->testRole = Role::create(['name' => 'test-role']);
});

// INDEX TESTS
test('index page is displayed for user with manage roles permission', function () {
    $response = $this
        ->actingAs($this->userWithPermission)
        ->get(route('cms.roles.index'));

    $response->assertOk();
    $response->assertViewIs('cms.roles.index');
    $response->assertViewHas('roles');
});

test('index page is forbidden for user without manage roles permission', function () {
    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->get(route('cms.roles.index'));

    $response->assertForbidden();
});

// SHOW TESTS
test('show page is displayed for user with manage roles permission', function () {
    $response = $this
        ->actingAs($this->userWithPermission)
        ->get(route('cms.roles.show', $this->testRole));

    $response->assertOk();
    $response->assertViewIs('cms.roles.show');
    $response->assertViewHas(['role', 'users']);
});

test('show page is forbidden for user without manage roles permission', function () {
    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->get(route('cms.roles.show', $this->testRole));

    $response->assertForbidden();
});

// CREATE TESTS
test('create page is displayed for user with manage roles permission', function () {
    $response = $this
        ->actingAs($this->userWithPermission)
        ->get(route('cms.roles.create'));

    $response->assertOk();
    $response->assertViewIs('cms.roles.create');
    $response->assertViewHas('permissions');
});

test('create page is forbidden for user without manage roles permission', function () {
    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->get(route('cms.roles.create'));

    $response->assertForbidden();
});

// STORE TESTS
test('store creates a new role when user has manage roles permission', function () {
    // Create some permissions to assign to the role
    Permission::create(['name' => 'test permission 1']);
    Permission::create(['name' => 'test permission 2']);

    $roleData = [
        'name' => 'New Test Role',
        'permissions' => ['test permission 1', 'test permission 2'],
    ];

    $response = $this
        ->actingAs($this->userWithPermission)
        ->post(route('cms.roles.store'), $roleData);

    $response->assertRedirect();
    $this->assertDatabaseHas('roles', [
        'name' => 'new-test-role', // Name is slugified
    ]);

    $newRole = Role::where('name', 'new-test-role')->first();
    $this->assertTrue($newRole->hasPermissionTo('test permission 1'));
    $this->assertTrue($newRole->hasPermissionTo('test permission 2'));
});

test('store is forbidden for user without manage roles permission', function () {
    $roleData = [
        'name' => 'New Test Role',
    ];

    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->post(route('cms.roles.store'), $roleData);

    $response->assertForbidden();
    $this->assertDatabaseMissing('roles', [
        'name' => 'new-test-role',
    ]);
});

// EDIT TESTS
test('edit page is displayed for user with manage roles permission', function () {
    $response = $this
        ->actingAs($this->userWithPermission)
        ->get(route('cms.roles.edit', $this->testRole));

    $response->assertOk();
    $response->assertViewIs('cms.roles.edit');
    $response->assertViewHas(['role', 'permissions']);
});

test('edit page is forbidden for user without manage roles permission', function () {
    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->get(route('cms.roles.edit', $this->testRole));

    $response->assertForbidden();
});

// UPDATE TESTS
test('update modifies a role when user has manage roles permission', function () {
    // Create some permissions to assign to the role
    Permission::create(['name' => 'test permission 1']);
    Permission::create(['name' => 'test permission 2']);

    $updatedData = [
        'name' => 'Updated Test Role',
        'permissions' => ['test permission 1', 'test permission 2'],
    ];

    $response = $this
        ->actingAs($this->userWithPermission)
        ->put(route('cms.roles.update', $this->testRole), $updatedData);

    $response->assertRedirect();
    $this->assertDatabaseHas('roles', [
        'id' => $this->testRole->id,
        'name' => 'updated-test-role', // Name is slugified
    ]);

    $this->testRole->refresh();
    $this->assertTrue($this->testRole->hasPermissionTo('test permission 1'));
    $this->assertTrue($this->testRole->hasPermissionTo('test permission 2'));
});

test('update is forbidden for user without manage roles permission', function () {
    $updatedData = [
        'name' => 'Updated Test Role',
    ];

    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->put(route('cms.roles.update', $this->testRole), $updatedData);

    $response->assertForbidden();
    $this->assertDatabaseMissing('roles', [
        'id' => $this->testRole->id,
        'name' => 'updated-test-role',
    ]);
});

test('super-admin role cannot be updated', function () {
    $superAdminRole = Role::findByName('super-admin');

    $updatedData = [
        'name' => 'Trying to Update Super Admin',
    ];

    $response = $this
        ->actingAs($this->userWithPermission)
        ->put(route('cms.roles.update', $superAdminRole), $updatedData);

    $response->assertRedirect(route('cms.roles.show', $superAdminRole));
    $this->assertDatabaseMissing('roles', [
        'id' => $superAdminRole->id,
        'name' => 'trying-to-update-super-admin',
    ]);
});

test('admin role cannot be updated', function () {
    $adminRole = Role::findByName('admin');

    $updatedData = [
        'name' => 'Trying to Update Admin',
    ];

    $response = $this
        ->actingAs($this->userWithPermission)
        ->put(route('cms.roles.update', $adminRole), $updatedData);

    $response->assertRedirect(route('cms.roles.show', $adminRole));
    $this->assertDatabaseMissing('roles', [
        'id' => $adminRole->id,
        'name' => 'trying-to-update-admin',
    ]);
});

// DESTROY TESTS
test('destroy deletes a role when user has manage roles permission', function () {
    $response = $this
        ->actingAs($this->userWithPermission)
        ->delete(route('cms.roles.destroy', $this->testRole));

    $response->assertRedirect(route('cms.roles.index'));
    $this->assertDatabaseMissing('roles', [
        'id' => $this->testRole->id,
    ]);
});

test('destroy is forbidden for user without manage roles permission', function () {
    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->delete(route('cms.roles.destroy', $this->testRole));

    $response->assertForbidden();
    $this->assertDatabaseHas('roles', [
        'id' => $this->testRole->id,
    ]);
});

test('super-admin role cannot be deleted', function () {
    $superAdminRole = Role::findByName('super-admin');

    $response = $this
        ->actingAs($this->userWithPermission)
        ->delete(route('cms.roles.destroy', $superAdminRole));

    $response->assertRedirect(route('cms.roles.show', $superAdminRole));
    $this->assertDatabaseHas('roles', [
        'id' => $superAdminRole->id,
    ]);
});

test('admin role cannot be deleted', function () {
    $adminRole = Role::findByName('admin');

    $response = $this
        ->actingAs($this->userWithPermission)
        ->delete(route('cms.roles.destroy', $adminRole));

    $response->assertRedirect(route('cms.roles.show', $adminRole));
    $this->assertDatabaseHas('roles', [
        'id' => $adminRole->id,
    ]);
});

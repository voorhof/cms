<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Create necessary permissions
    Permission::create(['name' => 'access cms']);
    Permission::create(['name' => 'manage users']);
    Permission::create(['name' => 'manage roles']);

    // Create roles
    Role::create(['name' => 'super-admin']);
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'editor']);

    // Create a user with permission to manage users and roles
    $this->userWithPermission = User::factory()->create(['name' => 'Admin User']);
    $this->userWithPermission->givePermissionTo(['access cms', 'manage users', 'manage roles']);

    // Create a user without permission to manage users
    $this->userWithoutPermission = User::factory()->create(['name' => 'Regular User']);
    $this->userWithoutPermission->givePermissionTo('access cms');

    // Create a super-admin user
    $this->superAdmin = User::factory()->create(['name' => 'Super Admin']);
    $this->superAdmin->assignRole('super-admin');
    $this->superAdmin->givePermissionTo(['access cms', 'manage users', 'manage roles']);

    // Create a test user that will be manipulated in tests
    $this->testUser = User::factory()->create(['name' => 'Test User']);
});

// INDEX TESTS
test('index page is displayed for user with access cms permission', function () {
    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->get(route(config('cms.route_name_prefix').'.users.index'));

    $response->assertOk();
    $response->assertViewIs('cms.users.index');
    $response->assertViewHas('users');
});

// SHOW TESTS
test('show page is displayed for user with access cms permission', function () {
    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->get(route(config('cms.route_name_prefix').'.users.show', $this->testUser));

    $response->assertOk();
    $response->assertViewIs('cms.users.show');
    $response->assertViewHas('user');
});

// CREATE TESTS
test('create page is displayed for user with manage users permission', function () {
    $response = $this
        ->actingAs($this->userWithPermission)
        ->get(route(config('cms.route_name_prefix').'.users.create'));

    $response->assertOk();
    $response->assertViewIs('cms.users.create');
    $response->assertViewHas('roles');
});

test('create page is forbidden for user without manage users permission', function () {
    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->get(route(config('cms.route_name_prefix').'.users.create'));

    $response->assertForbidden();
});

// STORE TESTS
test('store creates a new user when user has manage users permission', function () {
    $userData = [
        'name' => 'New Test User',
        'email' => 'newtestuser@example.com',
        'role' => 'editor',
    ];

    $response = $this
        ->actingAs($this->userWithPermission)
        ->post(route(config('cms.route_name_prefix').'.users.store'), $userData);

    $response->assertRedirect();
    $this->assertDatabaseHas('users', [
        'name' => 'New Test User',
        'email' => 'newtestuser@example.com',
    ]);

    $newUser = User::where('email', 'newtestuser@example.com')->first();
    $newUser->refresh(); // Refresh the model to ensure the role is loaded
    $this->assertTrue($newUser->hasRole('editor'));
});

test('store is forbidden for user without manage users permission', function () {
    $userData = [
        'name' => 'New Test User',
        'email' => 'newtestuser@example.com',
    ];

    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->post(route(config('cms.route_name_prefix').'.users.store'), $userData);

    $response->assertForbidden();
    $this->assertDatabaseMissing('users', [
        'email' => 'newtestuser@example.com',
    ]);
});

// EDIT TESTS
test('edit page is displayed for user with manage users permission', function () {
    $response = $this
        ->actingAs($this->userWithPermission)
        ->get(route(config('cms.route_name_prefix').'.users.edit', $this->testUser));

    $response->assertOk();
    $response->assertViewIs('cms.users.edit');
    $response->assertViewHas(['user', 'roles']);
});

test('edit page is forbidden for user without manage users permission', function () {
    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->get(route(config('cms.route_name_prefix').'.users.edit', $this->testUser));

    $response->assertForbidden();
});

// UPDATE TESTS
test('update modifies a user when user has manage users permission', function () {
    $updatedData = [
        'name' => 'Updated Test User',
        'email' => 'updatedtestuser@example.com',
        'role' => 'editor',
    ];

    $response = $this
        ->actingAs($this->userWithPermission)
        ->put(route(config('cms.route_name_prefix').'.users.update', $this->testUser), $updatedData);

    $response->assertRedirect();
    $this->assertDatabaseHas('users', [
        'id' => $this->testUser->id,
        'name' => 'Updated Test User',
        'email' => 'updatedtestuser@example.com',
    ]);

    $this->testUser->refresh();
    $this->assertTrue($this->testUser->hasRole('editor'));
});

test('update is forbidden for user without manage users permission', function () {
    $updatedData = [
        'name' => 'Updated Test User',
        'email' => 'updatedtestuser@example.com',
    ];

    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->put(route(config('cms.route_name_prefix').'.users.update', $this->testUser), $updatedData);

    $response->assertForbidden();
    $this->assertDatabaseMissing('users', [
        'id' => $this->testUser->id,
        'name' => 'Updated Test User',
    ]);
});

test('super-admin user cannot be updated by non-super-admin', function () {
    // Create a super-admin user
    $superAdminUser = User::factory()->create(['name' => 'Another Super Admin']);
    $role = Role::findByName('super-admin');
    $superAdminUser->assignRole($role);

    // Refresh the model to ensure the role is loaded
    $superAdminUser->refresh();

    // Verify that the user has the super-admin role
    $this->assertTrue($superAdminUser->hasRole('super-admin'));

    $updatedData = [
        'name' => 'Trying to Update Super Admin',
        'email' => 'updatedsuperadmin@example.com',
    ];

    $response = $this
        ->actingAs($this->userWithPermission)
        ->put(route(config('cms.route_name_prefix').'.users.update', $superAdminUser), $updatedData);

    $response->assertRedirect(route(config('cms.route_name_prefix').'.users.show', $superAdminUser));
    $this->assertDatabaseMissing('users', [
        'id' => $superAdminUser->id,
        'name' => 'Trying to Update Super Admin',
    ]);
});

// DESTROY TESTS
test('destroy soft deletes a user when user has manage users permission', function () {
    $response = $this
        ->actingAs($this->userWithPermission)
        ->delete(route(config('cms.route_name_prefix').'.users.destroy', $this->testUser));

    $response->assertRedirect(route(config('cms.route_name_prefix').'.users.index'));
    $this->assertSoftDeleted('users', [
        'id' => $this->testUser->id,
    ]);
});

test('destroy is forbidden for user without manage users permission', function () {
    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->delete(route(config('cms.route_name_prefix').'.users.destroy', $this->testUser));

    $response->assertForbidden();
    $this->assertNotSoftDeleted('users', [
        'id' => $this->testUser->id,
    ]);
});

test('super-admin user cannot be deleted by non-super-admin', function () {
    // Create a super-admin user
    $superAdminUser = User::factory()->create(['name' => 'Another Super Admin']);
    $role = Role::findByName('super-admin');
    $superAdminUser->assignRole($role);

    // Refresh the model to ensure the role is loaded
    $superAdminUser->refresh();

    // Verify that the user has the super-admin role
    $this->assertTrue($superAdminUser->hasRole('super-admin'));

    $response = $this
        ->actingAs($this->userWithPermission)
        ->delete(route(config('cms.route_name_prefix').'.users.destroy', $superAdminUser));

    $response->assertRedirect(route(config('cms.route_name_prefix').'.users.show', $superAdminUser));
    $this->assertNotSoftDeleted('users', [
        'id' => $superAdminUser->id,
    ]);
});

// TRASH TESTS
test('trash page is displayed for user with manage users permission', function () {
    // First soft delete a user
    $this->testUser->delete();

    $response = $this
        ->actingAs($this->userWithPermission)
        ->get(route(config('cms.route_name_prefix').'.users.trash'));

    $response->assertOk();
    $response->assertViewIs('cms.users.trash');
    $response->assertViewHas('users');
});

test('trash page is forbidden for user without manage users permission', function () {
    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->get(route(config('cms.route_name_prefix').'.users.trash'));

    $response->assertForbidden();
});

// RESTORE TESTS
test('restore restores a soft deleted user when user has manage users permission', function () {
    // First soft delete a user
    $this->testUser->delete();

    $response = $this
        ->actingAs($this->userWithPermission)
        ->patch(route(config('cms.route_name_prefix').'.users.restore', $this->testUser));

    $response->assertRedirect(route(config('cms.route_name_prefix').'.users.show', $this->testUser));
    $this->assertNotSoftDeleted('users', [
        'id' => $this->testUser->id,
    ]);
});

test('restore is forbidden for user without manage users permission', function () {
    // First soft delete a user
    $this->testUser->delete();

    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->patch(route(config('cms.route_name_prefix').'.users.restore', $this->testUser));

    $response->assertForbidden();
    $this->assertSoftDeleted('users', [
        'id' => $this->testUser->id,
    ]);
});

// DELETE TESTS
test('delete permanently deletes a soft deleted user when user has manage users permission', function () {
    // First soft delete a user
    $this->testUser->delete();

    $response = $this
        ->actingAs($this->userWithPermission)
        ->delete(route(config('cms.route_name_prefix').'.users.delete', $this->testUser));

    $response->assertRedirect(route(config('cms.route_name_prefix').'.users.trash'));
    $this->assertDatabaseMissing('users', [
        'id' => $this->testUser->id,
    ]);
});

test('delete is forbidden for user without manage users permission', function () {
    // First soft delete a user
    $this->testUser->delete();

    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->delete(route(config('cms.route_name_prefix').'.users.delete', $this->testUser));

    $response->assertForbidden();
    $this->assertSoftDeleted('users', [
        'id' => $this->testUser->id,
    ]);
});

// EMPTY TRASH TESTS
test('emptyTrash deletes all soft deleted users when user has manage users permission', function () {
    // First soft delete a user
    $this->testUser->delete();

    $response = $this
        ->actingAs($this->userWithPermission)
        ->delete(route(config('cms.route_name_prefix').'.users.emptyTrash'));

    $response->assertRedirect(route(config('cms.route_name_prefix').'.users.index'));
    $this->assertDatabaseMissing('users', [
        'id' => $this->testUser->id,
    ]);
});

test('emptyTrash is forbidden for user without manage users permission', function () {
    // First soft delete a user
    $this->testUser->delete();

    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->delete(route(config('cms.route_name_prefix').'.users.emptyTrash'));

    $response->assertForbidden();
    $this->assertSoftDeleted('users', [
        'id' => $this->testUser->id,
    ]);
});

<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CmsUserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $userWithPermission;

    protected User $userWithoutPermission;

    protected User $superAdmin;

    protected User $testUser;

    protected function setUp(): void
    {
        parent::setUp();

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
    }

    // INDEX TESTS
    public function test_index_page_is_displayed_for_user_with_access_cms_permission()
    {
        $response = $this
            ->actingAs($this->userWithoutPermission)
            ->get(route(config('cms.route_name_prefix').'.users.index'));

        $response->assertOk();
        $response->assertViewIs('cms.users.index');
        $response->assertViewHas('users');
    }

    // SHOW TESTS
    public function test_show_page_is_displayed_for_user_with_access_cms_permission()
    {
        $response = $this
            ->actingAs($this->userWithoutPermission)
            ->get(route(config('cms.route_name_prefix').'.users.show', $this->testUser));

        $response->assertOk();
        $response->assertViewIs('cms.users.show');
        $response->assertViewHas('user');
    }

    // CREATE TESTS
    public function test_create_page_is_displayed_for_user_with_manage_users_permission()
    {
        $response = $this
            ->actingAs($this->userWithPermission)
            ->get(route(config('cms.route_name_prefix').'.users.create'));

        $response->assertOk();
        $response->assertViewIs('cms.users.create');
        $response->assertViewHas('roles');
    }

    public function test_create_page_is_forbidden_for_user_without_manage_users_permission()
    {
        $response = $this
            ->actingAs($this->userWithoutPermission)
            ->get(route(config('cms.route_name_prefix').'.users.create'));

        $response->assertForbidden();
    }

    // STORE TESTS
    public function test_store_creates_a_new_user_when_user_has_manage_users_permission()
    {
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
    }

    public function test_store_is_forbidden_for_user_without_manage_users_permission()
    {
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
    }

    // EDIT TESTS
    public function test_edit_page_is_displayed_for_user_with_manage_users_permission()
    {
        $response = $this
            ->actingAs($this->userWithPermission)
            ->get(route(config('cms.route_name_prefix').'.users.edit', $this->testUser));

        $response->assertOk();
        $response->assertViewIs('cms.users.edit');
        $response->assertViewHas(['user', 'roles']);
    }

    public function test_edit_page_is_forbidden_for_user_without_manage_users_permission()
    {
        $response = $this
            ->actingAs($this->userWithoutPermission)
            ->get(route(config('cms.route_name_prefix').'.users.edit', $this->testUser));

        $response->assertForbidden();
    }

    // UPDATE TESTS
    public function test_update_modifies_a_user_when_user_has_manage_users_permission()
    {
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
    }

    public function test_update_is_forbidden_for_user_without_manage_users_permission()
    {
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
    }

    public function test_super_admin_user_cannot_be_updated_by_non_super_admin()
    {
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
    }

    // DESTROY TESTS
    public function test_destroy_soft_deletes_a_user_when_user_has_manage_users_permission()
    {
        $response = $this
            ->actingAs($this->userWithPermission)
            ->delete(route(config('cms.route_name_prefix').'.users.destroy', $this->testUser));

        $response->assertRedirect(route(config('cms.route_name_prefix').'.users.index'));
        $this->assertSoftDeleted('users', [
            'id' => $this->testUser->id,
        ]);
    }

    public function test_destroy_is_forbidden_for_user_without_manage_users_permission()
    {
        $response = $this
            ->actingAs($this->userWithoutPermission)
            ->delete(route(config('cms.route_name_prefix').'.users.destroy', $this->testUser));

        $response->assertForbidden();
        $this->assertNotSoftDeleted('users', [
            'id' => $this->testUser->id,
        ]);
    }

    public function test_super_admin_user_cannot_be_deleted_by_non_super_admin()
    {
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
    }

    // TRASH TESTS
    public function test_trash_page_is_displayed_for_user_with_manage_users_permission()
    {
        // First soft delete a user
        $this->testUser->delete();

        $response = $this
            ->actingAs($this->userWithPermission)
            ->get(route(config('cms.route_name_prefix').'.users.viewTrash'));

        $response->assertOk();
        $response->assertViewIs('cms.users.trash');
        $response->assertViewHas('users');
    }

    public function test_trash_page_is_forbidden_for_user_without_manage_users_permission()
    {
        $response = $this
            ->actingAs($this->userWithoutPermission)
            ->get(route(config('cms.route_name_prefix').'.users.viewTrash'));

        $response->assertForbidden();
    }

    // RESTORE TESTS
    public function test_restore_restores_a_soft_deleted_user_when_user_has_manage_users_permission()
    {
        // First soft delete a user
        $this->testUser->delete();

        $response = $this
            ->actingAs($this->userWithPermission)
            ->patch(route(config('cms.route_name_prefix').'.users.restore', $this->testUser));

        $response->assertRedirect(route(config('cms.route_name_prefix').'.users.show', $this->testUser));
        $this->assertNotSoftDeleted('users', [
            'id' => $this->testUser->id,
        ]);
    }

    public function test_restore_is_forbidden_for_user_without_manage_users_permission()
    {
        // First soft delete a user
        $this->testUser->delete();

        $response = $this
            ->actingAs($this->userWithoutPermission)
            ->patch(route(config('cms.route_name_prefix').'.users.restore', $this->testUser));

        $response->assertForbidden();
        $this->assertSoftDeleted('users', [
            'id' => $this->testUser->id,
        ]);
    }

    // DELETE TESTS
    public function test_delete_permanently_deletes_a_soft_deleted_user_when_user_has_manage_users_permission()
    {
        // First soft delete a user
        $this->testUser->delete();

        $response = $this
            ->actingAs($this->userWithPermission)
            ->delete(route(config('cms.route_name_prefix').'.users.delete', $this->testUser));

        $response->assertRedirect(route(config('cms.route_name_prefix').'.users.viewTrash'));
        $this->assertDatabaseMissing('users', [
            'id' => $this->testUser->id,
        ]);
    }

    public function test_delete_is_forbidden_for_user_without_manage_users_permission()
    {
        // First soft delete a user
        $this->testUser->delete();

        $response = $this
            ->actingAs($this->userWithoutPermission)
            ->delete(route(config('cms.route_name_prefix').'.users.delete', $this->testUser));

        $response->assertForbidden();
        $this->assertSoftDeleted('users', [
            'id' => $this->testUser->id,
        ]);
    }

    // EMPTY TRASH TESTS
    public function test_empty_trash_deletes_all_soft_deleted_users_when_user_has_manage_users_permission()
    {
        // First soft delete a user
        $this->testUser->delete();

        $response = $this
            ->actingAs($this->userWithPermission)
            ->delete(route(config('cms.route_name_prefix').'.users.emptyTrash'));

        $response->assertRedirect(route(config('cms.route_name_prefix').'.users.index'));
        $this->assertDatabaseMissing('users', [
            'id' => $this->testUser->id,
        ]);
    }

    public function test_empty_trash_is_forbidden_for_user_without_manage_users_permission()
    {
        // First soft delete a user
        $this->testUser->delete();

        $response = $this
            ->actingAs($this->userWithoutPermission)
            ->delete(route(config('cms.route_name_prefix').'.users.emptyTrash'));

        $response->assertForbidden();
        $this->assertSoftDeleted('users', [
            'id' => $this->testUser->id,
        ]);
    }
}

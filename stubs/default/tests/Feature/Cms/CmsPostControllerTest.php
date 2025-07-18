<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CmsPostControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $userWithFullPermission;

    protected User $userWithLimitedPermission;

    protected User $userWithoutPermission;

    protected User $superAdmin;

    protected Post $testPost;

    protected function setUp(): void
    {
        parent::setUp();

        // Create necessary permissions
        Permission::create(['name' => 'access cms']);
        Permission::create(['name' => 'manage posts']);
        Permission::create(['name' => 'create post']);
        Permission::create(['name' => 'edit post']);
        Permission::create(['name' => 'publish post']);

        // Create roles
        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'editor']);

        // Create a user with full post-management permissions
        $this->userWithFullPermission = User::factory()->create(['name' => 'Admin User']);
        $this->userWithFullPermission->givePermissionTo(['access cms', 'manage posts']);

        // Create a user with limited post-permissions
        $this->userWithLimitedPermission = User::factory()->create(['name' => 'Editor User']);
        $this->userWithLimitedPermission->givePermissionTo(['access cms', 'create post', 'edit post', 'publish post']);

        // Create a user without post-permissions
        $this->userWithoutPermission = User::factory()->create(['name' => 'Regular User']);
        $this->userWithoutPermission->givePermissionTo('access cms');

        // Create a super-admin user
        $this->superAdmin = User::factory()->create(['name' => 'Super Admin']);
        $this->superAdmin->assignRole('super-admin');
        $this->superAdmin->givePermissionTo(['access cms', 'manage posts']);

        // Create a test post that will be manipulated in tests
        $this->testPost = Post::factory()->create([
            'title' => 'Test Post',
            'body' => 'This is a test post content.',
            'user_id' => $this->userWithFullPermission->id,
        ]);
    }

    // INDEX TESTS
    public function test_index_page_is_displayed_for_user_with_access_cms_permission()
    {
        $response = $this
            ->actingAs($this->userWithoutPermission)
            ->get(route(config('cms.route_name_prefix').'.posts.index'));

        $response->assertOk();
        $response->assertViewIs('cms.posts.index');
        $response->assertViewHas(['posts', 'postsTrashCount']);
    }

    // SHOW TESTS
    public function test_show_page_is_displayed_for_user_with_access_cms_permission()
    {
        $response = $this
            ->actingAs($this->userWithoutPermission)
            ->get(route(config('cms.route_name_prefix').'.posts.show', $this->testPost));

        $response->assertOk();
        $response->assertViewIs('cms.posts.show');
        $response->assertViewHas('post');
    }

    // CREATE TESTS
    public function test_create_page_is_displayed_for_user_with_create_post_permission()
    {
        $response = $this
            ->actingAs($this->userWithLimitedPermission)
            ->get(route(config('cms.route_name_prefix').'.posts.create'));

        $response->assertOk();
        $response->assertViewIs('cms.posts.create');
    }

    public function test_create_page_is_displayed_for_user_with_manage_posts_permission()
    {
        $response = $this
            ->actingAs($this->userWithFullPermission)
            ->get(route(config('cms.route_name_prefix').'.posts.create'));

        $response->assertOk();
        $response->assertViewIs('cms.posts.create');
    }

    public function test_create_page_is_forbidden_for_user_without_create_post_permission()
    {
        $response = $this
            ->actingAs($this->userWithoutPermission)
            ->get(route(config('cms.route_name_prefix').'.posts.create'));

        $response->assertForbidden();
    }

    // STORE TESTS
    public function test_store_creates_a_new_post_when_user_has_create_post_permission()
    {
        $postData = [
            'title' => 'New Test Post',
            'body' => 'This is a new test post content.',
        ];

        $response = $this
            ->actingAs($this->userWithLimitedPermission)
            ->post(route(config('cms.route_name_prefix').'.posts.store'), $postData);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'title' => 'New Test Post',
            'body' => 'This is a new test post content.',
            'user_id' => $this->userWithLimitedPermission->id,
        ]);
    }

    public function test_store_creates_a_new_post_when_user_has_manage_posts_permission()
    {
        $postData = [
            'title' => 'New Admin Post',
            'body' => 'This is a new admin post content.',
        ];

        $response = $this
            ->actingAs($this->userWithFullPermission)
            ->post(route(config('cms.route_name_prefix').'.posts.store'), $postData);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'title' => 'New Admin Post',
            'body' => 'This is a new admin post content.',
            'user_id' => $this->userWithFullPermission->id,
        ]);
    }

    public function test_store_is_forbidden_for_user_without_create_post_permission()
    {
        $postData = [
            'title' => 'Unauthorized Post',
            'body' => 'This post should not be created.',
        ];

        $response = $this
            ->actingAs($this->userWithoutPermission)
            ->post(route(config('cms.route_name_prefix').'.posts.store'), $postData);

        $response->assertForbidden();
        $this->assertDatabaseMissing('posts', [
            'title' => 'Unauthorized Post',
        ]);
    }

    // EDIT TESTS
    public function test_edit_page_is_displayed_for_user_with_edit_post_permission()
    {
        $response = $this
            ->actingAs($this->userWithLimitedPermission)
            ->get(route(config('cms.route_name_prefix').'.posts.edit', $this->testPost));

        $response->assertOk();
        $response->assertViewIs('cms.posts.edit');
        $response->assertViewHas('post');
    }

    public function test_edit_page_is_displayed_for_user_with_manage_posts_permission()
    {
        $response = $this
            ->actingAs($this->userWithFullPermission)
            ->get(route(config('cms.route_name_prefix').'.posts.edit', $this->testPost));

        $response->assertOk();
        $response->assertViewIs('cms.posts.edit');
        $response->assertViewHas('post');
    }

    public function test_edit_page_is_forbidden_for_user_without_edit_post_permission()
    {
        $response = $this
            ->actingAs($this->userWithoutPermission)
            ->get(route(config('cms.route_name_prefix').'.posts.edit', $this->testPost));

        $response->assertForbidden();
    }

    // UPDATE TESTS
    public function test_update_modifies_a_post_when_user_has_edit_post_permission()
    {
        $updatedData = [
            'title' => 'Updated Test Post',
            'body' => 'This is an updated test post content.',
        ];

        $response = $this
            ->actingAs($this->userWithLimitedPermission)
            ->put(route(config('cms.route_name_prefix').'.posts.update', $this->testPost), $updatedData);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'id' => $this->testPost->id,
            'title' => 'Updated Test Post',
            'body' => 'This is an updated test post content.',
        ]);
    }

    public function test_update_modifies_a_post_when_user_has_manage_posts_permission()
    {
        $updatedData = [
            'title' => 'Admin Updated Post',
            'body' => 'This is an admin updated post content.',
        ];

        $response = $this
            ->actingAs($this->userWithFullPermission)
            ->put(route(config('cms.route_name_prefix').'.posts.update', $this->testPost), $updatedData);

        $response->assertRedirect();
        $this->assertDatabaseHas('posts', [
            'id' => $this->testPost->id,
            'title' => 'Admin Updated Post',
            'body' => 'This is an admin updated post content.',
        ]);
    }

    public function test_update_is_forbidden_for_user_without_edit_post_permission()
    {
        $updatedData = [
            'title' => 'Unauthorized Update',
            'body' => 'This update should not be applied.',
        ];

        $response = $this
            ->actingAs($this->userWithoutPermission)
            ->put(route(config('cms.route_name_prefix').'.posts.update', $this->testPost), $updatedData);

        $response->assertForbidden();
        $this->assertDatabaseMissing('posts', [
            'id' => $this->testPost->id,
            'title' => 'Unauthorized Update',
        ]);
    }

    // PUBLISH TESTS
    public function test_publish_action_works_for_user_with_publish_post_permission()
    {
        $response = $this
            ->actingAs($this->userWithLimitedPermission)
            ->patch(route(config('cms.route_name_prefix').'.posts.publish', $this->testPost));

        $response->assertRedirect();
        $this->testPost->refresh();
        $this->assertNotNull($this->testPost->published_at);
        $this->assertEquals($this->userWithLimitedPermission->id, $this->testPost->published_by);
    }

    public function test_publish_action_works_for_user_with_manage_posts_permission()
    {
        $response = $this
            ->actingAs($this->userWithFullPermission)
            ->patch(route(config('cms.route_name_prefix').'.posts.publish', $this->testPost));

        $response->assertRedirect();
        $this->testPost->refresh();
        $this->assertNotNull($this->testPost->published_at);
        $this->assertEquals($this->userWithFullPermission->id, $this->testPost->published_by);
    }

    public function test_publish_action_is_forbidden_for_user_without_publish_post_permission()
    {
        $response = $this
            ->actingAs($this->userWithoutPermission)
            ->patch(route(config('cms.route_name_prefix').'.posts.publish', $this->testPost));

        $response->assertForbidden();
        $this->testPost->refresh();
        $this->assertNull($this->testPost->published_at);
        $this->assertNull($this->testPost->published_by);
    }

    public function test_unpublish_action_works_for_user_with_publish_post_permission()
    {
        // First publish the post
        $this->testPost->published_at = now();
        $this->testPost->published_by = $this->userWithLimitedPermission->id;
        $this->testPost->save();

        $response = $this
            ->actingAs($this->userWithLimitedPermission)
            ->patch(route(config('cms.route_name_prefix').'.posts.publish', $this->testPost));

        $response->assertRedirect();
        $this->testPost->refresh();
        $this->assertNull($this->testPost->published_at);
        $this->assertNull($this->testPost->published_by);
    }

    // DESTROY TESTS
    public function test_destroy_soft_deletes_a_post_when_user_has_manage_posts_permission()
    {
        $response = $this
            ->actingAs($this->userWithFullPermission)
            ->delete(route(config('cms.route_name_prefix').'.posts.destroy', $this->testPost));

        $response->assertRedirect(route(config('cms.route_name_prefix').'.posts.index'));
        $this->assertSoftDeleted('posts', [
            'id' => $this->testPost->id,
        ]);
    }

    public function test_destroy_is_forbidden_for_user_without_manage_posts_permission()
    {
        $response = $this
            ->actingAs($this->userWithLimitedPermission)
            ->delete(route(config('cms.route_name_prefix').'.posts.destroy', $this->testPost));

        $response->assertForbidden();
        $this->assertNotSoftDeleted('posts', [
            'id' => $this->testPost->id,
        ]);
    }

    // TRASH TESTS
    public function test_trash_page_is_displayed_for_user_with_manage_posts_permission()
    {
        // First soft delete a post
        $this->testPost->delete();

        $response = $this
            ->actingAs($this->userWithFullPermission)
            ->get(route(config('cms.route_name_prefix').'.posts.trash'));

        $response->assertOk();
        $response->assertViewIs('cms.posts.trash');
        $response->assertViewHas('posts');
    }

    public function test_trash_page_is_forbidden_for_user_without_manage_posts_permission()
    {
        $response = $this
            ->actingAs($this->userWithLimitedPermission)
            ->get(route(config('cms.route_name_prefix').'.posts.trash'));

        $response->assertForbidden();
    }

    // RESTORE TESTS
    public function test_restore_restores_a_soft_deleted_post_when_user_has_manage_posts_permission()
    {
        // First soft delete a post
        $this->testPost->delete();

        $response = $this
            ->actingAs($this->userWithFullPermission)
            ->patch(route(config('cms.route_name_prefix').'.posts.restore', $this->testPost));

        $response->assertRedirect(route(config('cms.route_name_prefix').'.posts.show', $this->testPost));
        $this->assertNotSoftDeleted('posts', [
            'id' => $this->testPost->id,
        ]);
    }

    public function test_restore_is_forbidden_for_user_without_manage_posts_permission()
    {
        // First soft delete a post
        $this->testPost->delete();

        $response = $this
            ->actingAs($this->userWithLimitedPermission)
            ->patch(route(config('cms.route_name_prefix').'.posts.restore', $this->testPost));

        $response->assertForbidden();
        $this->assertSoftDeleted('posts', [
            'id' => $this->testPost->id,
        ]);
    }

    // DELETE TESTS
    public function test_delete_permanently_deletes_a_soft_deleted_post_when_user_has_manage_posts_permission()
    {
        // First soft delete a post
        $this->testPost->delete();

        $response = $this
            ->actingAs($this->userWithFullPermission)
            ->delete(route(config('cms.route_name_prefix').'.posts.delete', $this->testPost));

        $response->assertRedirect(route(config('cms.route_name_prefix').'.posts.trash'));
        $this->assertDatabaseMissing('posts', [
            'id' => $this->testPost->id,
        ]);
    }

    public function test_delete_is_forbidden_for_user_without_manage_posts_permission()
    {
        // First soft delete a post
        $this->testPost->delete();

        $response = $this
            ->actingAs($this->userWithLimitedPermission)
            ->delete(route(config('cms.route_name_prefix').'.posts.delete', $this->testPost));

        $response->assertForbidden();
        $this->assertSoftDeleted('posts', [
            'id' => $this->testPost->id,
        ]);
    }

    // EMPTY TRASH TESTS
    public function test_empty_trash_deletes_all_soft_deleted_posts_when_user_has_manage_posts_permission()
    {
        // First soft delete a post
        $this->testPost->delete();

        $response = $this
            ->actingAs($this->userWithFullPermission)
            ->delete(route(config('cms.route_name_prefix').'.posts.emptyTrash'));

        $response->assertRedirect(route(config('cms.route_name_prefix').'.posts.index'));
        $this->assertDatabaseMissing('posts', [
            'id' => $this->testPost->id,
        ]);
    }

    public function test_empty_trash_is_forbidden_for_user_without_manage_posts_permission()
    {
        // First soft delete a post
        $this->testPost->delete();

        $response = $this
            ->actingAs($this->userWithLimitedPermission)
            ->delete(route(config('cms.route_name_prefix').'.posts.emptyTrash'));

        $response->assertForbidden();
        $this->assertSoftDeleted('posts', [
            'id' => $this->testPost->id,
        ]);
    }
}

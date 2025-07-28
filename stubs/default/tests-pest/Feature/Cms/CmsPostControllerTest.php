<?php

use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
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
});

// INDEX TESTS
test('index page is displayed for user with access cms permission', function () {
    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->get(route(config('cms.route_name_prefix').'.posts.index'));

    $response->assertOk();
    $response->assertViewIs('cms.posts.index');
    $response->assertViewHas(['posts', 'postsTrashCount']);
});

// SHOW TESTS
test('show page is displayed for user with access cms permission', function () {
    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->get(route(config('cms.route_name_prefix').'.posts.show', $this->testPost));

    $response->assertOk();
    $response->assertViewIs('cms.posts.show');
    $response->assertViewHas('post');
});

// CREATE TESTS
test('create page is displayed for user with create post permission', function () {
    $response = $this
        ->actingAs($this->userWithLimitedPermission)
        ->get(route(config('cms.route_name_prefix').'.posts.create'));

    $response->assertOk();
    $response->assertViewIs('cms.posts.create');
});

test('create page is displayed for user with manage posts permission', function () {
    $response = $this
        ->actingAs($this->userWithFullPermission)
        ->get(route(config('cms.route_name_prefix').'.posts.create'));

    $response->assertOk();
    $response->assertViewIs('cms.posts.create');
});

test('create page is forbidden for user without create post permission', function () {
    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->get(route(config('cms.route_name_prefix').'.posts.create'));

    $response->assertForbidden();
});

// STORE TESTS
test('store creates a new post when user has create post permission', function () {
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
});

test('store creates a new post when user has manage posts permission', function () {
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
});

test('store is forbidden for user without create post permission', function () {
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
});

// EDIT TESTS
test('edit page is displayed for user with edit post permission', function () {
    $response = $this
        ->actingAs($this->userWithLimitedPermission)
        ->get(route(config('cms.route_name_prefix').'.posts.edit', $this->testPost));

    $response->assertOk();
    $response->assertViewIs('cms.posts.edit');
    $response->assertViewHas('post');
});

test('edit page is displayed for user with manage posts permission', function () {
    $response = $this
        ->actingAs($this->userWithFullPermission)
        ->get(route(config('cms.route_name_prefix').'.posts.edit', $this->testPost));

    $response->assertOk();
    $response->assertViewIs('cms.posts.edit');
    $response->assertViewHas('post');
});

test('edit page is forbidden for user without edit post permission', function () {
    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->get(route(config('cms.route_name_prefix').'.posts.edit', $this->testPost));

    $response->assertForbidden();
});

// UPDATE TESTS
test('update modifies a post when user has edit post permission', function () {
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
});

test('update modifies a post when user has manage posts permission', function () {
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
});

test('update is forbidden for user without edit post permission', function () {
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
});

// PUBLISH TESTS
test('publish action works for user with publish post permission', function () {
    $response = $this
        ->actingAs($this->userWithLimitedPermission)
        ->patch(route(config('cms.route_name_prefix').'.posts.publish', $this->testPost));

    $response->assertRedirect();
    $this->testPost->refresh();
    $this->assertNotNull($this->testPost->published_at);
    $this->assertEquals($this->userWithLimitedPermission->id, $this->testPost->published_by);
});

test('publish action works for user with manage posts permission', function () {
    $response = $this
        ->actingAs($this->userWithFullPermission)
        ->patch(route(config('cms.route_name_prefix').'.posts.publish', $this->testPost));

    $response->assertRedirect();
    $this->testPost->refresh();
    $this->assertNotNull($this->testPost->published_at);
    $this->assertEquals($this->userWithFullPermission->id, $this->testPost->published_by);
});

test('publish action is forbidden for user without publish post permission', function () {
    $response = $this
        ->actingAs($this->userWithoutPermission)
        ->patch(route(config('cms.route_name_prefix').'.posts.publish', $this->testPost));

    $response->assertForbidden();
    $this->testPost->refresh();
    $this->assertNull($this->testPost->published_at);
    $this->assertNull($this->testPost->published_by);
});

test('unpublish action works for user with publish post permission', function () {
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
});

// DESTROY TESTS
test('destroy soft deletes a post when user has manage posts permission', function () {
    $response = $this
        ->actingAs($this->userWithFullPermission)
        ->delete(route(config('cms.route_name_prefix').'.posts.destroy', $this->testPost));

    $response->assertRedirect(route(config('cms.route_name_prefix').'.posts.index'));
    $this->assertSoftDeleted('posts', [
        'id' => $this->testPost->id,
    ]);
});

test('destroy is forbidden for user without manage posts permission', function () {
    $response = $this
        ->actingAs($this->userWithLimitedPermission)
        ->delete(route(config('cms.route_name_prefix').'.posts.destroy', $this->testPost));

    $response->assertForbidden();
    $this->assertNotSoftDeleted('posts', [
        'id' => $this->testPost->id,
    ]);
});

// TRASH TESTS
test('trash page is displayed for user with manage posts permission', function () {
    // First soft delete a post
    $this->testPost->delete();

    $response = $this
        ->actingAs($this->userWithFullPermission)
        ->get(route(config('cms.route_name_prefix').'.posts.viewTrash'));

    $response->assertOk();
    $response->assertViewIs('cms.posts.trash');
    $response->assertViewHas('posts');
});

test('trash page is forbidden for user without manage posts permission', function () {
    $response = $this
        ->actingAs($this->userWithLimitedPermission)
        ->get(route(config('cms.route_name_prefix').'.posts.viewTrash'));

    $response->assertForbidden();
});

// RESTORE TESTS
test('restore restores a soft deleted post when user has manage posts permission', function () {
    // First soft delete a post
    $this->testPost->delete();

    $response = $this
        ->actingAs($this->userWithFullPermission)
        ->patch(route(config('cms.route_name_prefix').'.posts.restore', $this->testPost));

    $response->assertRedirect(route(config('cms.route_name_prefix').'.posts.show', $this->testPost));
    $this->assertNotSoftDeleted('posts', [
        'id' => $this->testPost->id,
    ]);
});

test('restore is forbidden for user without manage posts permission', function () {
    // First soft delete a post
    $this->testPost->delete();

    $response = $this
        ->actingAs($this->userWithLimitedPermission)
        ->patch(route(config('cms.route_name_prefix').'.posts.restore', $this->testPost));

    $response->assertForbidden();
    $this->assertSoftDeleted('posts', [
        'id' => $this->testPost->id,
    ]);
});

// DELETE TESTS
test('delete permanently deletes a soft deleted post when user has manage posts permission', function () {
    // First soft delete a post
    $this->testPost->delete();

    $response = $this
        ->actingAs($this->userWithFullPermission)
        ->delete(route(config('cms.route_name_prefix').'.posts.delete', $this->testPost));

    $response->assertRedirect(route(config('cms.route_name_prefix').'.posts.viewTrash'));
    $this->assertDatabaseMissing('posts', [
        'id' => $this->testPost->id,
    ]);
});

test('delete is forbidden for user without manage posts permission', function () {
    // First soft delete a post
    $this->testPost->delete();

    $response = $this
        ->actingAs($this->userWithLimitedPermission)
        ->delete(route(config('cms.route_name_prefix').'.posts.delete', $this->testPost));

    $response->assertForbidden();
    $this->assertSoftDeleted('posts', [
        'id' => $this->testPost->id,
    ]);
});

// EMPTY TRASH TESTS
test('emptyTrash deletes all soft deleted posts when user has manage posts permission', function () {
    // First soft delete a post
    $this->testPost->delete();

    $response = $this
        ->actingAs($this->userWithFullPermission)
        ->delete(route(config('cms.route_name_prefix').'.posts.emptyTrash'));

    $response->assertRedirect(route(config('cms.route_name_prefix').'.posts.index'));
    $this->assertDatabaseMissing('posts', [
        'id' => $this->testPost->id,
    ]);
});

test('emptyTrash is forbidden for user without manage posts permission', function () {
    // First soft delete a post
    $this->testPost->delete();

    $response = $this
        ->actingAs($this->userWithLimitedPermission)
        ->delete(route(config('cms.route_name_prefix').'.posts.emptyTrash'));

    $response->assertForbidden();
    $this->assertSoftDeleted('posts', [
        'id' => $this->testPost->id,
    ]);
});

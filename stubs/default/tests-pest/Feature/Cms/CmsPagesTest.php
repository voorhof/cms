<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;

test('cms dashboard page is displayed', function () {
    Permission::create(['name' => 'access cms']);
    $user = User::factory()->create();
    $user->givePermissionTo('access cms');

    $response = $this
        ->actingAs($user)
        ->get('/cms');

    $response->assertOk();
});

test('cms dashboard page is forbidden without access', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/cms');

    $response->assertForbidden();
});

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CmsPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_cms_dashboard_page_is_displayed(): void
    {
        Permission::create(['name' => 'access cms']);
        $user = User::factory()->create();
        $user->givePermissionTo('access cms');

        $response = $this
            ->actingAs($user)
            ->get('/cms');

        $response->assertOk();
    }

    public function test_cms_dashboard_page_is_forbidden(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/cms');

        $response->assertForbidden();
    }
}

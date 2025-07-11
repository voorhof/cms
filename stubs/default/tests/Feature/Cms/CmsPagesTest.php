<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_cms_dashboard_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/cms');

        $response->assertOk();
    }
}

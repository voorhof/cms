<?php

namespace Database\Seeders\Cms;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create fake factory contributors their posts
        User::factory(3)
            ->contributor()
            ->has(Post::factory(2))
            ->has(Post::factory(4)->published())
            ->has(Post::factory(1)->deleted())
            ->create();
    }
}

<?php

namespace Database\Seeders;

use Database\Seeders\Cms\RolesAndPermissionsSeeder;
use Database\Seeders\Cms\UserSeeder;
use Illuminate\Database\Seeder;

class CmsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
        ]);
    }
}

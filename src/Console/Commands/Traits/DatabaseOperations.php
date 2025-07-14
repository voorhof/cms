<?php

namespace Voorhof\Cms\Console\Commands\Traits;

use Illuminate\Support\Facades\Artisan;

/**
 * Database Operations Trait
 *
 * Manages database migration
 */
trait DatabaseOperations
{
    /**
     * Migrate fresh and seed the database.
     *
     * @return bool Success status
     */
    protected function migrateFreshSeed(): bool
    {
        Artisan::call('migrate:fresh --seed --seeder=CmsSeeder');

        return true;
    }
}

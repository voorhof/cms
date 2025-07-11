<?php

namespace Voorhof\Cms;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Voorhof\Cms\Console\Commands\InstallCmsCommand;

class CmsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCmsCommand::class,
            ]);
        }
    }

    /**
     * DeferrableProvider services.
     */
    public function provides(): array
    {
        return [
            InstallCmsCommand::class,
        ];
    }
}

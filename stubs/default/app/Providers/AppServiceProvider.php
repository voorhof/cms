<?php

namespace App\Providers;

use App\Services\Cms\Contracts\PostServiceInterface;
use App\Services\Cms\Contracts\RoleServiceInterface;
use App\Services\Cms\Contracts\UserServiceInterface;
use App\Services\Cms\PostService;
use App\Services\Cms\RoleService;
use App\Services\Cms\UserService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * Note: In Laravel 12 service providers are registered in bootstrap/providers.php
     */
    public function register(): void
    {
        // Bind CMS UserServiceInterface to CMS UserService implementation
        $this->app->bind(UserServiceInterface::class, UserService::class);

        // Bind CMS PostServiceInterface to CMS PostService implementation
        $this->app->bind(PostServiceInterface::class, PostService::class);

        // Bind CMS RoleServiceInterface to CMS RoleService implementation
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Implicitly grant "super-admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}

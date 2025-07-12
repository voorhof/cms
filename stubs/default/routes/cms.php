<?php

use App\Http\Controllers\Cms\CmsController;
use Illuminate\Support\Facades\Route;

/**
 * CMS routes
 */
Route::middleware(config('cms.route_middleware'))
    ->prefix(config('cms.route_uri_prefix'))
    ->name(config('cms.route_name_prefix').'.')
    ->group(function () {
        /**
         * Dashboard page
         */
        Route::get('/', [CmsController::class, 'dashboard'])->name('dashboard');
    });

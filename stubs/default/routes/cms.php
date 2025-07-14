<?php

use App\Http\Controllers\Cms\CmsController;
use App\Http\Controllers\Cms\CmsUserController;
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

        /**
         * User resource controller
         */
        Route::get('/users/trash', [CmsUserController::class, 'trash'])->name('users.trash');
        Route::delete('/users/trash', [CmsUserController::class, 'emptyTrash'])->name('users.emptyTrash');
        Route::patch('/users/{user}/restore', [CmsUserController::class, 'restore'])->name('users.restore')->withTrashed();
        Route::delete('/users/{user}/delete', [CmsUserController::class, 'delete'])->name('users.delete')->withTrashed();
        Route::resource('users', CmsUserController::class);
    });

<?php

use App\Http\Controllers\Cms\CmsController;
use App\Http\Controllers\Cms\CmsPostController;
use App\Http\Controllers\Cms\CmsRoleController;
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
         * Post resource controller
         */
        Route::get('/posts/trash', [CmsPostController::class, 'viewTrash'])->name('posts.viewTrash');
        Route::delete('/posts/trash', [CmsPostController::class, 'emptyTrash'])->name('posts.emptyTrash');
        Route::patch('/posts/{post}/restore', [CmsPostController::class, 'restore'])->name('posts.restore')->withTrashed();
        Route::delete('/posts/{post}/delete', [CmsPostController::class, 'delete'])->name('posts.delete')->withTrashed();
        Route::patch('/posts/{post}/publish', [CmsPostController::class, 'publish'])->name('posts.publish');
        Route::resource('posts', CmsPostController::class);

        /**
         * User resource controller
         */
        Route::get('/users/trash', [CmsUserController::class, 'viewTrash'])->name('users.viewTrash');
        Route::delete('/users/trash', [CmsUserController::class, 'emptyTrash'])->name('users.emptyTrash');
        Route::patch('/users/{user}/restore', [CmsUserController::class, 'restore'])->name('users.restore')->withTrashed();
        Route::delete('/users/{user}/delete', [CmsUserController::class, 'delete'])->name('users.delete')->withTrashed();
        Route::resource('users', CmsUserController::class);

        /**
         * Role resource controller
         */
        Route::resource('roles', CmsRoleController::class);
    });

<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;

class BaseCmsController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to all CMS controllers
     */
    public static function middleware(): array
    {
        return config('cms.route_middleware');
    }
}

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CMS route uri
    |--------------------------------------------------------------------------
    |
    | This value is the uri prefix for all CMS routes
    | https://laravel.com/docs/12.x/routing#route-group-prefixes
    |
    */
    'route_uri_prefix' => env('CMS_URI_PREFIX', 'cms'),

    /*
    |--------------------------------------------------------------------------
    | CMS route name
    |--------------------------------------------------------------------------
    |
    | This value is the name prefix for all CMS routes
    | https://laravel.com/docs/12.x/routing#route-group-name-prefixes
    |
    */

    'route_name_prefix' => env('CMS_NAME_PREFIX', 'cms'),

    /*
    |--------------------------------------------------------------------------
    | CMS route middleware
    |--------------------------------------------------------------------------
    |
    | Middleware applied to all CMS routes
    | Customize this to your CMS access logic
    |
    */

    'route_middleware' => [
        'auth',
        'verified',
        'can:access cms',
    ],

];

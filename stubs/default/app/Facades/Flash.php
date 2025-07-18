<?php

namespace App\Facades;

use App\Services\FlashMessageService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void success(string $message)
 * @method static void warning(string $message)
 * @method static void danger(string $message)
 * @method static void info(string $message)
 * @method static void flash(string $message, string $level = 'info')
 *
 * @see FlashMessageService
 */
class Flash extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return FlashMessageService::class;
    }
}

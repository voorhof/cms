<?php

namespace App\Services;

class FlashMessageService
{
    /**
     * Flash a success message to the session.
     *
     * @param  string  $message  The message to flash
     */
    public function success(string $message): void
    {
        $this->flash($message, 'success');
    }

    /**
     * Flash a warning message to the session.
     *
     * @param  string  $message  The message to flash
     */
    public function warning(string $message): void
    {
        $this->flash($message, 'warning');
    }

    /**
     * Flash a danger/error message to the session.
     *
     * @param  string  $message  The message to flash
     */
    public function danger(string $message): void
    {
        $this->flash($message, 'danger');
    }

    /**
     * Flash an info message to the session.
     *
     * @param  string  $message  The message to flash
     */
    public function info(string $message): void
    {
        $this->flash($message);
    }

    /**
     * Flash a message to the session with the specified level.
     *
     * @param  string  $message  The message to flash
     * @param  string  $level  The level of the message (success, warning, danger, info)
     */
    public function flash(string $message, string $level = 'info'): void
    {
        session()->flash('flash_message', $message);
        session()->flash('flash_level', $level);
    }
}

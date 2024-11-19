<?php

namespace App\Contracts\Services;

interface LogServiceInterface
{
    /**
     * Log any error message.
     *
     * @param string $title
     * @param string $message
     * @param ?string $trace
     * @return void
     */
    public function error(string $title, string $message, ?string $trace): void;
}

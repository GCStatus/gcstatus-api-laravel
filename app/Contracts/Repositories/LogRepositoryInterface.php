<?php

namespace App\Contracts\Repositories;

interface LogRepositoryInterface
{
    /**
     * Log any error message.
     *
     * @param string $title
     * @param string $message
     * @param ?string $trace
     * @return void
     */
    public function error(string $title, string $message, ?string $trace = null): void;

    /**
     * Log with context.
     *
     * @param string $title
     * @param array<string, mixed> $context
     * @return void
     */
    public function withContext(string $title, array $context): void;
}

<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Log;
use App\Contracts\Repositories\LogRepositoryInterface;

class LogRepository implements LogRepositoryInterface
{
    /**
     * Log any error message.
     *
     * @param string $title
     * @param string $message
     * @param ?string $trace
     * @return void
     */
    public function error(string $title, string $message, ?string $trace = null): void
    {
        Log::error($title, [
            'trace' => $trace,
            'message' => $message,
        ]);
    }

    /**
     * Log with context.
     *
     * @param string $title
     * @param array<string, mixed> $context
     * @return void
     */
    public function withContext(string $title, array $context): void
    {
        Log::info($title, $context);
    }
}

<?php

namespace App\Services;

use App\Contracts\Services\LogServiceInterface;
use App\Contracts\Repositories\LogRepositoryInterface;

class LogService implements LogServiceInterface
{
    /**
     * The log repository.
     *
     * @var \App\Contracts\Repositories\LogRepositoryInterface
     */
    private LogRepositoryInterface $logRepository;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Repositories\LogRepositoryInterface $logRepository
     * @return void
     */
    public function __construct(LogRepositoryInterface $logRepository)
    {
        $this->logRepository = $logRepository;
    }

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
        $this->logRepository->error($title, $message, $trace);
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
        $this->logRepository->withContext($title, $context);
    }
}

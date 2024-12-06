<?php

namespace Tests\Unit\Repositories;

use Exception;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;
use App\Contracts\Repositories\LogRepositoryInterface;

class LogRepositoryTest extends TestCase
{
    /**
     * The log repository.
     *
     * @var \App\Contracts\Repositories\LogRepositoryInterface
     */
    private LogRepositoryInterface $logRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->logRepository = app(LogRepositoryInterface::class);
    }

    /**
     * Test if can log an error correctly.
     *
     * @return void
     */
    public function test_if_can_log_an_error_correctly(): void
    {
        $title = 'Failed title text.';
        $message = 'Failed message text.';
        $trace = (new Exception('Testing exception throwing.'))->getTraceAsString();

        Log::shouldReceive('error')
            ->once()
            ->with($title, [
                'trace' => $trace,
                'message' => $message,
            ]);

        $this->logRepository->error($title, $message, $trace);
    }

    /**
     * Test if can log any with context.
     *
     * @return void
     */
    public function test_if_can_log_any_with_context(): void
    {
        $title = 'Failed title text.';
        $message = 'Failed message text.';
        $trace = (new Exception('Testing exception throwing.'))->getTraceAsString();

        $context = [
            'trace' => $trace,
            'message' => $message,
            'testing' => 'Asserting tested.',
        ];

        Log::shouldReceive('info')
            ->once()
            ->with($title, $context);

        $this->logRepository->withContext($title, $context);
    }
}

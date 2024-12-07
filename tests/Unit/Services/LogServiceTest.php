<?php

namespace Tests\Unit\Services;

use Mockery;
use Exception;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Services\LogService;
use App\Contracts\Services\LogServiceInterface;
use App\Contracts\Repositories\LogRepositoryInterface;

class LogServiceTest extends TestCase
{
    /**
     * The log service.
     *
     * @var \App\Contracts\Services\LogServiceInterface
     */
    private LogServiceInterface $logService;

    /**
     * The mock log repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $mockRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mockRepository = Mockery::mock(LogRepositoryInterface::class);

        /** @var \App\Contracts\Repositories\LogRepositoryInterface $logRepository */
        $logRepository = $this->mockRepository;
        $this->logService = new LogService($logRepository);
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

        $this->mockRepository
            ->shouldReceive('error')
            ->once()
            ->with($title, $message, $trace);

        $this->logService->error($title, $message, $trace);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
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

        $this->mockRepository
            ->shouldReceive('withContext')
            ->once()
            ->with($title, $context);

        $this->logService->withContext($title, $context);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Cleanup Mockery after each test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}

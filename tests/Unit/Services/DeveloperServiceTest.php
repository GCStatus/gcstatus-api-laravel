<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Repositories\DeveloperRepository;
use App\Contracts\Services\DeveloperServiceInterface;
use App\Contracts\Repositories\DeveloperRepositoryInterface;

class DeveloperServiceTest extends TestCase
{
    /**
     * The developer service.
     *
     * @var \App\Contracts\Services\DeveloperServiceInterface
     */
    private DeveloperServiceInterface $developerService;

    /**
     * The developer repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $developerRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->developerRepository = Mockery::mock(DeveloperRepositoryInterface::class);

        $this->app->instance(DeveloperRepositoryInterface::class, $this->developerRepository);

        $this->developerService = app(DeveloperServiceInterface::class);
    }

    /**
     * Test if DeveloperService uses the Developer repository correctly.
     *
     * @return void
     */
    public function test_Developer_service_uses_Developer_repository(): void
    {
        $this->app->instance(DeveloperRepositoryInterface::class, new DeveloperRepository());

        /** @var \App\Services\DeveloperService $developerService */
        $developerService = app(DeveloperServiceInterface::class);

        $this->assertInstanceOf(DeveloperRepository::class, $developerService->repository());
    }

    /**
     * Test if can find a developer by name.
     *
     * @return void
     */
    public function test_if_can_find_a_developer_by_name(): void
    {
        $name = fake()->name();

        $this->developerRepository
            ->shouldReceive('existsByName')
            ->once()
            ->with($name)
            ->andReturnTrue();

        $result = $this->developerService->existsByName($name);

        $this->assertTrue($result);
        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Tear down application tests.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}

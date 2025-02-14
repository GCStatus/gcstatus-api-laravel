<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Repositories\PublisherRepository;
use App\Contracts\Services\PublisherServiceInterface;
use App\Contracts\Repositories\PublisherRepositoryInterface;

class PublisherServiceTest extends TestCase
{
    /**
     * The publisher service.
     *
     * @var \App\Contracts\Services\PublisherServiceInterface
     */
    private PublisherServiceInterface $publisherService;

    /**
     * The publisher repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $publisherRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->publisherRepository = Mockery::mock(PublisherRepositoryInterface::class);

        $this->app->instance(PublisherRepositoryInterface::class, $this->publisherRepository);

        $this->publisherService = app(PublisherServiceInterface::class);
    }

    /**
     * Test if PublisherService uses the publisher repository correctly.
     *
     * @return void
     */
    public function test_publisher_service_uses_publisher_repository(): void
    {
        $this->app->instance(PublisherRepositoryInterface::class, new PublisherRepository());

        /** @var \App\Services\PublisherService $publisherService */
        $publisherService = app(PublisherServiceInterface::class);

        $this->assertInstanceOf(PublisherRepository::class, $publisherService->repository());
    }

    /**
     * Test if can find a publisher by name.
     *
     * @return void
     */
    public function test_if_can_find_a_publisher_by_name(): void
    {
        $name = fake()->name();

        $this->publisherRepository
            ->shouldReceive('existsByName')
            ->once()
            ->with($name)
            ->andReturnTrue();

        $result = $this->publisherService->existsByName($name);

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

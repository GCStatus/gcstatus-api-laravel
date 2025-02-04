<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Models\{Game, Heartable};
use App\Repositories\HeartableRepository;
use App\Contracts\Repositories\HeartableRepositoryInterface;
use App\Contracts\Services\{
    AuthServiceInterface,
    HeartableServiceInterface,
    HeartNotificationServiceInterface,
};

class HeartableServiceTest extends TestCase
{
    /**
     * The heartable repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $heartableRepository;

    /**
     * The auth service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $authService;

    /**
     * The heart notification service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $heartNotificationService;

    /**
     * The heartable service.
     *
     * @var \App\Contracts\Services\HeartableServiceInterface
     */
    private HeartableServiceInterface $heartableService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->authService = Mockery::mock(AuthServiceInterface::class);
        $this->heartableRepository = Mockery::mock(HeartableRepositoryInterface::class);
        $this->heartNotificationService = Mockery::mock(HeartNotificationServiceInterface::class);

        $this->app->instance(AuthServiceInterface::class, $this->authService);
        $this->app->instance(HeartableRepositoryInterface::class, $this->heartableRepository);
        $this->app->instance(HeartNotificationServiceInterface::class, $this->heartNotificationService);

        $this->heartableService = app(HeartableServiceInterface::class);
    }

    /**
     * Test if HeartableService uses the Heartable model correctly.
     *
     * @return void
     */
    public function test_heartable_repository_uses_heartable_model(): void
    {
        $this->app->instance(HeartableRepositoryInterface::class, app(HeartableRepository::class));

        /** @var \App\Services\HeartableService $heartableService */
        $heartableService = app(HeartableServiceInterface::class);

        $this->assertInstanceOf(HeartableRepository::class, $heartableService->repository());
    }

    /**
     * Test if can create a heartable.
     *
     * @return void
     */
    public function test_if_can_create_a_heartable(): void
    {
        $data = [
            'user_id' => 1,
            'heartable_id' => 1,
            'heartable_type' => Game::class,
        ];

        $heartable = Mockery::mock(Heartable::class);

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->withNoArgs()
            ->andReturn($data['user_id']);

        $this->heartableRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($heartable);

        $this->heartNotificationService
            ->shouldReceive('notifyNewHeart')
            ->once()
            ->with($heartable)
            ->andReturnNull();

        $result = $this->heartableService->create($data);

        $this->assertSame($result, $heartable);
        $this->assertInstanceOf(Heartable::class, $result);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can create a heartable for user if not exists.
     *
     * @return void
     */
    public function test_if_can_create_a_heartable_for_user_if_not_exists(): void
    {
        $userId = 1;

        $data = [
            'heartable_id' => 1,
            'heartable_type' => Game::class,
        ];

        $heartable = Mockery::mock(Heartable::class);
        $heartable->shouldReceive('getAttribute')->with('id')->andReturn($data['heartable_id']);

        $this->authService
            ->shouldReceive('getAuthId')
            ->twice()
            ->withNoArgs()
            ->andReturn($userId);

        $this->heartableRepository
            ->shouldReceive('findByUser')
            ->once()
            ->with($userId, $data)
            ->andReturnNull();

        $this->heartableRepository
            ->shouldReceive('create')
            ->once()
            ->with($data + [
                'user_id' => $userId,
            ])->andReturn($heartable);

        $this->heartNotificationService
            ->shouldReceive('notifyNewHeart')
            ->once()
            ->with($heartable)
            ->andReturnNull();

        $this->heartableService->toggle($data);

        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can delete a heartable for user if exists.
     *
     * @return void
     */
    public function test_if_can_delete_a_heartable_for_user_if_exists(): void
    {
        $userId = 1;

        $data = [
            'heartable_id' => 1,
            'heartable_type' => Game::class,
        ];

        $heartable = Mockery::mock(Heartable::class);
        $heartable->shouldReceive('getAttribute')->with('id')->andReturn($data['heartable_id']);

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->withNoArgs()
            ->andReturn($userId);

        $this->heartableRepository
            ->shouldReceive('findByUser')
            ->once()
            ->with($userId, $data)
            ->andReturn($heartable);

        $this->heartableRepository
            ->shouldReceive('delete')
            ->once()
            ->with($data['heartable_id'])
            ->andReturnTrue();

        $this->heartableService->toggle($data);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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

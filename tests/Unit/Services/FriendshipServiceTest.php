<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\FriendshipRepositoryInterface;
use Tests\TestCase;
use App\Contracts\Services\{
    AuthServiceInterface,
    FriendshipServiceInterface,
};
use Mockery;
use Mockery\MockInterface;

class FriendshipServiceTest extends TestCase
{
    /**
     * The auth service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $authService;

    /**
     * The friendship repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $friendshipRepository;

    /**
     * The friendship service.
     *
     * @var \App\Contracts\Services\FriendshipServiceInterface
     */
    private FriendshipServiceInterface $friendshipService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->authService = Mockery::mock(AuthServiceInterface::class);
        $this->friendshipRepository = Mockery::mock(FriendshipRepositoryInterface::class);

        $this->app->instance(AuthServiceInterface::class, $this->authService);
        $this->app->instance(FriendshipRepositoryInterface::class, $this->friendshipRepository);

        $this->friendshipService = app(FriendshipServiceInterface::class);
    }

    /**
     * Test if can check if friendship exists.
     *
     * @return void
     */
    public function test_if_can_check_if_friendship_exists(): void
    {
        $userId = 1;
        $friendId = 2;

        $this->authService
            ->shouldReceive('getAUthId')
            ->once()
            ->withNoArgs()
            ->andReturn($userId);

        $this->friendshipRepository
            ->shouldReceive('friendshipExists')
            ->once()
            ->with($userId, $friendId)
            ->andReturnTrue();

        $result = $this->friendshipService->exists($friendId);

        $this->assertTrue($result);

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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

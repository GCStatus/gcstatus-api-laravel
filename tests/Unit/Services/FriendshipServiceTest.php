<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Models\{User, Friendship};
use App\Contracts\Repositories\FriendshipRepositoryInterface;
use App\Contracts\Services\{
    FriendshipServiceInterface,
    FriendshipNotificationServiceInterface,
};

class FriendshipServiceTest extends TestCase
{
    /**
     * The friendship repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $friendshipRepository;

    /**
     * The friendship service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $friendshipNotificationService;

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

        $this->friendshipRepository = Mockery::mock(FriendshipRepositoryInterface::class);
        $this->friendshipNotificationService = Mockery::mock(FriendshipNotificationServiceInterface::class);

        $this->app->instance(FriendshipRepositoryInterface::class, $this->friendshipRepository);
        $this->app->instance(FriendshipNotificationServiceInterface::class, $this->friendshipNotificationService);

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

        $this->friendshipRepository
            ->shouldReceive('friendshipExists')
            ->once()
            ->with($userId, $friendId)
            ->andReturnTrue();

        $result = $this->friendshipService->exists($userId, $friendId);

        $this->assertTrue($result);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can create a new friendship and notify users.
     *
     * @return void
     */
    public function test_if_can_create_a_new_friendship_and_notify_users(): void
    {
        $userId = 1;
        $friendId = 2;

        $user = Mockery::mock(User::class);
        $friend = Mockery::mock(User::class);
        $friendship = Mockery::mock(Friendship::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $friend->shouldReceive('getAttribute')->with('id')->andReturn($friendId);

        $this->friendshipRepository
            ->shouldReceive('create')
            ->once()
            ->with([
                'user_id' => $userId,
                'friend_id' => $friendId,
            ])->andReturn($friendship);

        $this->friendshipNotificationService
            ->shouldReceive('notifyNewFriendship')
            ->once()
            ->with($friendship)
            ->andReturnNull();

        $this->friendshipService->create([
            'user_id' => $userId,
            'friend_id' => $friendId,
        ]);

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

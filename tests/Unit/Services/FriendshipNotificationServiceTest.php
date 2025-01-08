<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Illuminate\Support\Str;
use App\Models\{User, Friendship};
use App\Notifications\DatabaseNotification;
use App\Contracts\Services\FriendshipNotificationServiceInterface;

class FriendshipNotificationServiceTest extends TestCase
{
    /**
     * The friend request notification service.
     *
     * @var \App\Contracts\Services\FriendshipNotificationServiceInterface
     */
    private FriendshipNotificationServiceInterface $friendshipNotificationService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->friendshipNotificationService = app(FriendshipNotificationServiceInterface::class);
    }

    /**
     * Test if can notify a new friendship.
     *
     * @return void
     */
    public function test_if_can_notify_a_new_friendship(): void
    {
        $user = Mockery::mock(User::class);
        $friend = Mockery::mock(User::class);
        $friendship = Mockery::mock(Friendship::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $user->shouldReceive('getAttribute')->with('name')->andReturn(fake()->name());

        $friend->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $friend->shouldReceive('getAttribute')->with('name')->andReturn(fake()->name());

        $friendship->shouldReceive('getAttribute')->with('user')->andReturn($user);
        $friendship->shouldReceive('getAttribute')->with('friend')->andReturn($friend);

        /** @var \App\Models\User $friend */
        $friendName = Str::before($friend->name, ' ');

        $user->shouldReceive('notify')
            ->once()
            ->with(Mockery::on(function (DatabaseNotification $notification) use ($user, $friendName) {
                /** @var \App\Models\User $user */
                return $notification->data['userId'] === (string)$user->id &&
                    $notification->data['actionUrl'] === "/dummy-route" &&
                    $notification->data['title'] === "You and $friendName are now friends!";
            }));

        /** @var \App\Models\Friendship $friendship */
        $this->friendshipNotificationService->notifyNewFriendship($friendship);

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

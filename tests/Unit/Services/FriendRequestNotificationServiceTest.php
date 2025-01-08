<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\{User, FriendRequest};
use App\Notifications\DatabaseNotification;
use App\Contracts\Services\FriendRequestNotificationServiceInterface;

class FriendRequestNotificationServiceTest extends TestCase
{
    /**
     * The friend request notification service.
     *
     * @var \App\Contracts\Services\FriendRequestNotificationServiceInterface
     */
    private FriendRequestNotificationServiceInterface $friendRequestNotificationService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->friendRequestNotificationService = app(FriendRequestNotificationServiceInterface::class);
    }

    /**
     * Test if can notify a friend request addressee.
     *
     * @return void
     */
    public function test_if_can_notify_a_friend_request_addressee(): void
    {
        $user = Mockery::mock(User::class);
        $friendRequest = Mockery::mock(FriendRequest::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $friendRequest->shouldReceive('getAttribute')->with('addressee')->andReturn($user);

        $user->shouldReceive('notify')
            ->once()
            ->with(Mockery::on(function (DatabaseNotification $notification) use ($user) {
                /** @var \App\Models\User $user */
                return $notification->data['userId'] === (string)$user->id &&
                    $notification->data['actionUrl'] === "/dummy-route" &&
                    $notification->data['title'] === 'You have a new friend request.';
            }));

        /** @var \App\Models\FriendRequest $friendRequest */
        $this->friendRequestNotificationService->notifyNewFriendRequest($friendRequest);

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

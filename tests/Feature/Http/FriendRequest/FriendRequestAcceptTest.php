<?php

namespace Tests\Feature\Http\FriendRequest;

use Illuminate\Support\Str;
use App\Models\{FriendRequest, User};
use App\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyFriendship,
    HasDummyFriendRequest,
};

class FriendRequestAcceptTest extends BaseIntegrationTesting
{
    use HasDummyFriendship;
    use HasDummyFriendRequest;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy friend.
     *
     * @var \App\Models\User
     */
    private User $friend;

    /**
     * The user friend request.
     *
     * @var \App\Models\FriendRequest
     */
    private FriendRequest $friendRequest;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser();

        $this->friend = $this->createDummyUser();

        $this->friendRequest = $this->createDummyFriendRequest([
            'addressee_id' => $this->user->id,
            'requester_id' => $this->friend->id,
        ]);
    }

    /**
     * Test if can't accept a friend request if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_accept_a_friend_request_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->postJson(route('friends.request.accept', $this->friendRequest))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't accept a friend request if is not the addressee.
     *
     * @return void
     */
    public function test_if_cant_accept_a_friend_request_if_is_not_the_addressee(): void
    {
        $friendRequest = $this->createDummyFriendRequest([
            'requester_id' => $this->friend->id,
            'addressee_id' => $this->createDummyUser()->id,
        ]);

        $this->postJson(route('friends.request.accept', $friendRequest))
            ->assertForbidden()
            ->assertSee('You are not the friend request receiver, this action is unauthorized!');
    }

    /**
     * Test if can accept a friend request.
     *
     * @return void
     */
    public function test_if_can_accept_a_friend_request(): void
    {
        $this->postJson(route('friends.request.accept', $this->friendRequest))->assertOk();
    }

    /**
     * Test if can save the friend request acceptance on database.
     *
     * @return void
     */
    public function test_if_can_save_the_friend_request_acceptance_on_database(): void
    {
        $this->assertDatabaseEmpty('friendships');

        $this->postJson(route('friends.request.accept', $this->friendRequest))->assertOk();

        $this->assertDatabaseCount('friendships', 2)->assertDatabaseHas('friendships', [
            'user_id' => $this->user->id,
            'friend_id' => $this->friend->id,
        ])->assertDatabaseHas('friendships', [
            'friend_id' => $this->user->id,
            'user_id' => $this->friend->id,
        ]);
    }

    /**
     * Test if can notify both users for new friendship.
     *
     * @return void
     */
    public function test_if_can_notify_both_users_for_new_friendship(): void
    {
        Notification::fake();

        $this->postJson(route('friends.request.accept', $this->friendRequest))->assertOk();

        Notification::assertSentTimes(DatabaseNotification::class, 2);

        Notification::assertSentTo(
            $this->user,
            DatabaseNotification::class,
            function (DatabaseNotification $notificationInstance) {
                $friendName = Str::before($this->friend->name, ' ');

                $notification = [
                    'userId' => (string)$this->user->id,
                    'icon' => 'FaUserFriends',
                    'title' => "You and $friendName are now friends!",
                    'actionUrl' => "/dummy-route",
                ];

                return $notificationInstance->data = $notification;
            },
        );

        Notification::assertSentTo(
            $this->friend,
            DatabaseNotification::class,
            function (DatabaseNotification $notificationInstance) {
                $friendName = Str::before($this->user->name, ' ');

                $notification = [
                    'userId' => (string)$this->friend->id,
                    'icon' => 'FaUserFriends',
                    'title' => "You and $friendName are now friends!",
                    'actionUrl' => "/dummy-route",
                ];

                return $notificationInstance->data = $notification;
            },
        );
    }

    /**
     * Test if can save both users notification on database.
     *
     * @return void
     */
    public function test_if_can_save_both_users_notification_on_database(): void
    {
        $this->assertDatabaseEmpty('notifications');

        $this->postJson(route('friends.request.accept', $this->friendRequest))->assertOk();

        $forUser = Str::before($this->friend->name, ' ');
        $forFriend = Str::before($this->user->name, ' ');

        $notificationForUser = [
            'icon' => 'FaUserFriends',
            'title' => "You and $forUser are now friends!",
            'actionUrl' => "/dummy-route",
        ];

        $notificationForFriend = [
            'icon' => 'FaUserFriends',
            'title' => "You and $forFriend are now friends!",
            'actionUrl' => "/dummy-route",
        ];

        $this->assertDatabaseCount('notifications', 2)->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->user->id,
            'notifiable_type' => $this->user::class,
            'data' => json_encode($notificationForUser),
        ])->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->friend->id,
            'notifiable_type' => $this->friend::class,
            'data' => json_encode($notificationForFriend),
        ]);
    }

    /**
     * Test if can remove the friend request from database after acceptance.
     *
     * @return void
     */
    public function test_if_can_remove_the_friend_request_from_database_after_acceptance(): void
    {
        $this->assertDatabaseCount('friend_requests', 1);

        $this->postJson(route('friends.request.accept', $this->friendRequest))->assertOk();

        $this->assertDatabaseEmpty('friend_requests');
    }

    /**
     * Test if can't duplicate friendships on database if one of them already exists.
     *
     * @return void
     */
    public function test_if_cant_duplicate_friendships_on_database_if_one_of_them_already_exists(): void
    {
        $this->createDummyFriendship([
            'user_id' => $this->friend->id,
            'friend_id' => $this->user->id,
        ]);

        $this->assertDatabaseCount('friendships', 1);

        $this->postJson(route('friends.request.accept', $this->friendRequest))->assertOk();

        $this->assertDatabaseCount('friendships', 2);
    }
}

<?php

namespace Tests\Feature\Http\FriendRequest;

use App\Models\User;
use App\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{
    HasDummyFriendship,
    HasDummyFriendRequest,
};

class FriendRequestSendTest extends BaseIntegrationTesting
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
     * The dummy addressee.
     *
     * @var \App\Models\User
     */
    private User $addressee;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser();
        $this->addressee = $this->createDummyUser();
    }

    /**
     * Get a valid payload.
     *
     * @return array<string, int>
     */
    private function getValidPayload(): array
    {
        return [
            'addressee_id' => $this->addressee->id,
        ];
    }

    /**
     * Test if can't send a friend request if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_send_a_friend_request_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->postJson(route('friends.request.send'), $this->getValidPayload())
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't send a request for himself.
     *
     * @return void
     */
    public function test_if_cant_send_a_request_for_himself(): void
    {
        $this->postJson(route('friends.request.send'), [
            'addressee_id' => $this->user->id,
        ])->assertBadRequest()
            ->assertSee('The friend request can not be yourself!');
    }

    /**
     * Test if can't duplicate a friend request.
     *
     * @return void
     */
    public function test_if_cant_duplicate_a_friend_request(): void
    {
        $data = $this->getValidPayload();

        $this->createDummyFriendRequest([
            'requester_id' => $this->user->id,
            ...$data,
        ]);

        $this->postJson(route('friends.request.send'), $data)
            ->assertConflict()
            ->assertSee('You already sent a friend request for this user. Please, await for the approve or declinal.');
    }

    /**
     * Test if can send a friend request.
     *
     * @return void
     */
    public function test_if_can_send_a_friend_request(): void
    {
        $this->postJson(route('friends.request.send'), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can save the friend request on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_friend_request_on_database_correctly(): void
    {
        $this->postJson(route('friends.request.send'), $data = $this->getValidPayload())->assertOk();

        $this->assertDatabaseCount('friend_requests', 1)->assertDatabaseHas('friend_requests', [
            'requester_id' => $this->user->id,
            'addressee_id' => $data['addressee_id'],
        ]);
    }

    /**
     * Test if can send a notification for the addressee.
     *
     * @return void
     */
    public function test_if_can_send_a_notification_for_the_addressee(): void
    {
        Notification::fake();

        $this->postJson(route('friends.request.send'), $this->getValidPayload())->assertOk();

        $notification = [
            'userId' => (string)$this->addressee->id,
            'icon' => 'FaUserFriends',
            'title' => 'You have a new friend request.',
            'actionUrl' => "/dummy-route",
        ];

        Notification::assertSentTo(
            $this->addressee,
            DatabaseNotification::class,
            function (DatabaseNotification $notificationInstance) use ($notification) {
                return $notificationInstance->data === $notification;
            }
        );
    }

    /**
     * Test if can create the notification for addressee.
     *
     * @return void
     */
    public function test_if_can_create_the_notifciation_for_addressee(): void
    {
        $this->postJson(route('friends.request.send'), $this->getValidPayload())->assertOk();

        $notification = [
            'icon' => 'FaUserFriends',
            'title' => 'You have a new friend request.',
            'actionUrl' => "/dummy-route",
        ];

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->addressee->id,
            'data' => json_encode($notification),
            'notifiable_type' => $this->addressee::class,
        ]);
    }

    /**
     * Test if can create a new friendship for a mutual friend request.
     *
     * @return void
     */
    public function test_if_can_create_a_new_friendship_for_a_mutual_friend_request(): void
    {
        $this->createDummyFriendRequest([
            'addressee_id' => $this->user->id,
            'requester_id' => $this->addressee->id,
        ]);

        $this->postJson(route('friends.request.send'), $this->getValidPayload())->assertOk();

        $this->assertDatabaseEmpty('friend_requests')
            ->assertDatabaseCount('friendships', 2)
            ->assertDatabaseHas('friendships', [
                'user_id' => $this->user->id,
                'friend_id' => $this->addressee->id,
            ])->assertDatabaseHas('friendships', [
                'friend_id' => $this->user->id,
                'user_id' => $this->addressee->id,
            ]);
    }

    /**
     * Test if can't create friend request notification if have mutual friend request.
     *
     * @return void
     */
    public function test_if_cant_create_friend_request_notification_if_have_mutual_friend_request(): void
    {
        Notification::fake();

        $this->createDummyFriendRequest([
            'addressee_id' => $this->user->id,
            'requester_id' => $this->addressee->id,
        ]);

        $this->postJson(route('friends.request.send'), $this->getValidPayload())->assertOk();

        $notification = [
            'userId' => (string)$this->addressee->id,
            'icon' => 'FaUserFriends',
            'title' => 'You have a new friend request.',
            'actionUrl' => "/dummy-route",
        ];

        Notification::assertNotSentTo(
            $this->addressee,
            DatabaseNotification::class,
            function (DatabaseNotification $notificationInstance) use ($notification) {
                return $notificationInstance->data === $notification;
            }
        );
    }

    /**
     * Test if can't create duplicated friendships if have mutual friend requests.
     *
     * @return void
     */
    public function test_if_cant_create_duplicated_friendships_if_have_mutual_friend_requests(): void
    {
        $this->createDummyFriendship([
            'friend_id' => $this->user->id,
            'user_id' => $this->addressee->id,
        ]);

        $this->createDummyFriendship([
            'user_id' => $this->user->id,
            'friend_id' => $this->addressee->id,
        ]);

        $this->postJson(route('friends.request.send'), $this->getValidPayload())
            ->assertConflict()
            ->assertSee('You are already friend of the given user!');
    }

    /**
     * Test if can create remaining friendship if user already have the friendship with addressee.
     *
     * @return void
     */
    public function test_if_can_create_remaining_friendship_if_user_already_have_the_friendship_with_addressee(): void
    {
        $this->createDummyFriendship([
            'friend_id' => $this->user->id,
            'user_id' => $this->addressee->id,
        ]);

        $this->createDummyFriendRequest([
            'addressee_id' => $this->user->id,
            'requester_id' => $this->addressee->id,
        ]);

        $this->postJson(route('friends.request.send'), $this->getValidPayload())->assertOk();

        $this->assertDatabaseCount('friendships', 2)->assertDatabaseHas('friendships', [
            'user_id' => $this->user->id,
            'friend_id' => $this->addressee->id,
        ])->assertDatabaseHas('friendships', [
            'friend_id' => $this->user->id,
            'user_id' => $this->addressee->id,
        ]);
    }

    /**
     * Test if can delete friend requests if have mutual friend requests.
     *
     * @return void
     */
    public function test_if_can_delete_friend_requests_if_have_mutual_friend_requests(): void
    {
        $this->createDummyFriendRequest([
            'addressee_id' => $this->user->id,
            'requester_id' => $this->addressee->id,
        ]);

        $this->postJson(route('friends.request.send'), $this->getValidPayload())->assertOk();

        $this->assertDatabaseEmpty('friend_requests');
    }
}

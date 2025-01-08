<?php

namespace Tests\Feature\Http\FriendRequest;

use App\Models\{FriendRequest, User};
use Tests\Traits\HasDummyFriendRequest;
use Tests\Feature\Http\BaseIntegrationTesting;

class FriendRequestDeclineTest extends BaseIntegrationTesting
{
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
     * The dummy friend request.
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
     * Test if can't decline a friend request if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_decline_a_friend_request_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->postJson(route('friends.request.decline', $this->friendRequest))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't decline a friend request if not addressee.
     *
     * @return void
     */
    public function test_if_cant_decline_a_friend_request_if_not_addressee(): void
    {
        $friendRequest = $this->createDummyFriendRequest([
            'requester_id' => $this->friend->id,
            'addressee_id' => $this->createDummyUser()->id,
        ]);

        $this->postJson(route('friends.request.decline', $friendRequest))
            ->assertForbidden()
            ->assertSee('You are not the friend request receiver, this action is unauthorized!');
    }

    /**
     * Test if can decline a friend request.
     *
     * @return void
     */
    public function test_if_can_decline_a_friend_request(): void
    {
        $this->postJson(route('friends.request.decline', $this->friendRequest))->assertOk();
    }

    /**
     * Test if can remove the friend request from database.
     *
     * @return void
     */
    public function test_if_can_remove_the_friend_request_from_database(): void
    {
        $this->assertDatabaseHas('friend_requests', [
            'id' => $this->friendRequest->id,
        ]);

        $this->postJson(route('friends.request.decline', $this->friendRequest))->assertOk();

        $this->assertDatabaseMissing('friend_requests', [
            'id' => $this->friendRequest->id,
        ]);
    }
}

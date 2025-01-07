<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Models\FriendRequest;
use App\Contracts\Services\{
    AuthServiceInterface,
    FriendshipServiceInterface,
    FriendRequestServiceInterface,
};
use App\Contracts\Repositories\FriendRequestRepositoryInterface;
use App\Exceptions\FriendRequest\{
    NotFriendRequestReceiverException,
    FriendRequestAlreadyExistsException,
    FriendRequestCantBeSentToYouException,
};

class FriendRequestServiceTest extends TestCase
{
    /**
     * The friend request repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $friendRequestRepository;

    /**
     * The auth service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $authService;

    /**
     * The friendship service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $friendshipService;

    /**
     * The friend request service.
     *
     * @var \App\Contracts\Services\FriendRequestServiceInterface
     */
    private FriendRequestServiceInterface $friendRequestService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->authService = Mockery::mock(AuthServiceInterface::class);
        $this->friendshipService = Mockery::mock(FriendshipServiceInterface::class);
        $this->friendRequestRepository = Mockery::mock(FriendRequestRepositoryInterface::class);

        $this->app->instance(AuthServiceInterface::class, $this->authService);
        $this->app->instance(FriendshipServiceInterface::class, $this->friendshipService);
        $this->app->instance(FriendRequestRepositoryInterface::class, $this->friendRequestRepository);

        $this->friendRequestService = app(FriendRequestServiceInterface::class);
    }

    /**
     * Test if can send a friend request for a random user.
     *
     * @return void
     */
    public function test_if_can_send_a_friend_request_for_a_random_user(): void
    {
        $userId = 1;
        $friendId = 2;

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->withNoArgs()
            ->andReturn($userId);

        $this->friendRequestRepository
            ->shouldReceive('exists')
            ->once()
            ->with($userId, $friendId)
            ->andReturnFalse();

        $this->friendRequestRepository
            ->shouldReceive('create')
            ->once()
            ->with([
                'requester_id' => $userId,
                'addressee_id' => $friendId,
            ]);

        $this->friendRequestRepository
            ->shouldReceive('reciprocalRequestExists')
            ->once()
            ->with($userId, $friendId)
            ->andReturnFalse();

        $this->friendshipService->shouldNotReceive('create');

        $this->friendRequestService->send($friendId);

        $this->assertEquals(5, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can create the mutual friendship if already received a friend request from addressee.
     *
     * @return void
     */
    public function test_if_can_create_the_mutual_friendship_if_already_received_a_friend_request_from_addressee(): void
    {
        $userId = 1;
        $friendId = 2;

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->withNoArgs()
            ->andReturn($userId);

        $this->friendRequestRepository
            ->shouldReceive('exists')
            ->once()
            ->with($userId, $friendId)
            ->andReturnFalse();

        $this->friendRequestRepository
            ->shouldReceive('create')
            ->once()
            ->with([
                'requester_id' => $userId,
                'addressee_id' => $friendId,
            ]);

        $this->friendRequestRepository
            ->shouldReceive('reciprocalRequestExists')
            ->once()
            ->with($userId, $friendId)
            ->andReturnTrue();

        $this->friendshipService
            ->shouldReceive('exists')
            ->once()
            ->with($userId, $friendId)
            ->andReturnFalse();

        $this->friendshipService
            ->shouldReceive('create')
            ->once()
            ->with([
                'user_id' => $userId,
                'friend_id' => $friendId,
            ]);

        $this->friendshipService
            ->shouldReceive('create')
            ->once()
            ->with([
                'user_id' => $friendId,
                'friend_id' => $userId,
            ]);

        $this->friendRequestRepository
            ->shouldReceive('deleteReciprocalRequests')
            ->once()
            ->with($userId, $friendId)
            ->andReturnNull();

        $this->friendRequestService->send($friendId);

        $this->assertEquals(8, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can't send a friend request if user is same as addressee.
     *
     * @return void
     */
    public function test_if_cant_send_a_friend_request_if_user_is_same_as_addressee(): void
    {
        $userId = 1;
        $friendId = 1;

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->withNoArgs()
            ->andReturn($userId);

        $this->expectException(FriendRequestCantBeSentToYouException::class);
        $this->expectExceptionMessage('The friend request can not be yourself!');

        $this->friendRequestService->send($friendId);
    }

    /**
     * Test if can't send a duplicated friend request.
     *
     * @return void
     */
    public function test_if_cant_send_a_duplicated_friend_request(): void
    {
        $userId = 1;
        $friendId = 2;

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->withNoArgs()
            ->andReturn($userId);

        $this->friendRequestRepository
            ->shouldReceive('exists')
            ->once()
            ->with($userId, $friendId)
            ->andReturnTrue();

        $this->expectException(FriendRequestAlreadyExistsException::class);
        $this->expectExceptionMessage('You already sent a friend request for this user. Please, await for the approve or declinal.');

        $this->friendRequestService->send($friendId);
    }

    /**
     * Test if can accept a friend request.
     *
     * @return void
     */
    public function test_if_can_accept_a_friend_request(): void
    {
        $id = 1;
        $userId = 1;
        $friendId = 2;

        $friendRequest = Mockery::mock(FriendRequest::class);

        $friendRequest->shouldReceive('getAttribte')->with('id')->andReturn($id);
        $friendRequest->shouldReceive('getAttribute')->with('addressee_id')->andReturn($userId);
        $friendRequest->shouldReceive('getAttribute')->with('requester_id')->andReturn($friendId);

        $this->friendRequestRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($id)
            ->andReturn($friendRequest);

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->withNoArgs()
            ->andReturn($userId);

        $this->friendshipService
            ->shouldReceive('exists')
            ->once()
            ->with($userId, $friendId)
            ->andReturnFalse();

        $this->friendshipService
            ->shouldReceive('create')
            ->once()
            ->with([
                'user_id' => $userId,
                'friend_id' => $friendId,
            ]);

        $this->friendshipService
            ->shouldReceive('create')
            ->once()
            ->with([
                'user_id' => $friendId,
                'friend_id' => $userId,
            ]);

        $this->friendRequestRepository
            ->shouldReceive('deleteReciprocalRequests')
            ->once()
            ->with($userId, $friendId)
            ->andReturnNull();

        $this->friendRequestService->accept($id);

        $this->assertEquals(6, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can't accept if not the friend request addressee.
     *
     * @return void
     */
    public function test_if_cant_accept_if_not_the_friend_request_addressee(): void
    {
        $id = 1;
        $userId = 1;
        $friendId = 2;

        $friendRequest = Mockery::mock(FriendRequest::class);

        $friendRequest->shouldReceive('getAttribte')->with('id')->andReturn($id);
        $friendRequest->shouldReceive('getAttribute')->with('addressee_id')->andReturn($userId);
        $friendRequest->shouldReceive('getAttribute')->with('requester_id')->andReturn($friendId);

        $this->friendRequestRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($id)
            ->andReturn($friendRequest);

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->withNoArgs()
            ->andReturn(99);

        $this->expectException(NotFriendRequestReceiverException::class);
        $this->expectExceptionMessage('You are not the friend request receiver, this action is unauthorized!');

        $this->friendshipService->shouldNotReceive('exists');

        $this->friendshipService->shouldNotReceive('create');

        $this->friendRequestRepository->shouldNotReceive('deleteReciprocalRequests');

        $this->friendRequestService->accept($id);

        $this->assertEquals(5, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can decline a friend request.
     *
     * @return void
     */
    public function test_if_can_decline_a_friend_request(): void
    {
        $id = 1;
        $userId = 1;
        $friendId = 2;

        $friendRequest = Mockery::mock(FriendRequest::class);

        $friendRequest->shouldReceive('getAttribte')->with('id')->andReturn($id);
        $friendRequest->shouldReceive('getAttribute')->with('addressee_id')->andReturn($userId);
        $friendRequest->shouldReceive('getAttribute')->with('requester_id')->andReturn($friendId);

        $this->friendRequestRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($id)
            ->andReturn($friendRequest);

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->withNoArgs()
            ->andReturn($userId);

        $friendRequest->shouldReceive('delete')->once()->withNoArgs();

        $this->friendRequestService->decline($id);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can't decline if not the friend request addressee.
     *
     * @return void
     */
    public function test_if_cant_decline_if_not_the_friend_request_addressee(): void
    {
        $id = 1;
        $userId = 1;
        $friendId = 2;

        $friendRequest = Mockery::mock(FriendRequest::class);

        $friendRequest->shouldReceive('getAttribte')->with('id')->andReturn($id);
        $friendRequest->shouldReceive('getAttribute')->with('addressee_id')->andReturn($userId);
        $friendRequest->shouldReceive('getAttribute')->with('requester_id')->andReturn($friendId);

        $this->friendRequestRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($id)
            ->andReturn($friendRequest);

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->withNoArgs()
            ->andReturn(99);

        $this->expectException(NotFriendRequestReceiverException::class);
        $this->expectExceptionMessage('You are not the friend request receiver, this action is unauthorized!');

        $friendRequest->shouldNotReceive('delete');

        $this->friendRequestService->decline($id);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Tear down testing environment.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}

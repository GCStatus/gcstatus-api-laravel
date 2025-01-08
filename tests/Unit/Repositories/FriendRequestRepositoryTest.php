<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Closure;
use Tests\TestCase;
use App\Models\{User, FriendRequest};
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\FriendRequestRepository;
use App\Contracts\Repositories\FriendRequestRepositoryInterface;

class FriendRequestRepositoryTest extends TestCase
{
    /**
     * The friend request repository.
     *
     * @var \App\Contracts\Repositories\FriendRequestRepositoryInterface
     */
    private FriendRequestRepositoryInterface $friendRequestRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->friendRequestRepository = app(FriendRequestRepositoryInterface::class);
    }

    /**
     * Test if FriendRequestRepository uses the FriendRequest model correctly.
     *
     * @return void
     */
    public function test_friend_request_repository_uses_friend_request_model(): void
    {
        /** @var \App\Repositories\FriendRequestRepository $friendRequestRepository */
        $friendRequestRepository = $this->friendRequestRepository;

        $this->assertInstanceOf(FriendRequest::class, $friendRequestRepository->model());
    }

    /**
     * Test if can check if a friend request exists.
     *
     * @return void
     */
    public function test_if_can_check_if_a_friend_request_exists(): void
    {
        $userId = 1;
        $friendId = 2;

        $user = Mockery::mock(User::class);
        $friend = Mockery::mock(User::class);
        $builder = Mockery::mock(Builder::class);
        $friendRequest = Mockery::mock(FriendRequest::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $friend->shouldReceive('getAttribute')->with('id')->andReturn($friendId);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('requester_id', $userId)
            ->andReturnSelf();

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('addressee_id', $friendId)
            ->andReturnSelf();

        $builder
            ->shouldReceive('exists')
            ->once()
            ->withNoArgs()
            ->andReturnTrue();

        $friendRequest->shouldReceive('query')->once()->withNoArgs()->andReturn($builder);

        $repoMock = Mockery::mock(FriendRequestRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->andReturn($friendRequest);

        /** @var \App\Contracts\Repositories\FriendRequestRepositoryInterface $repoMock */
        $result = $repoMock->exists($userId, $friendId);

        $this->assertTrue($result);

        $this->assertEquals(5, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can check if a reciprocal friend request exists.
     *
     * @return void
     */
    public function test_if_can_check_if_a_reciprocal_friend_request_exists(): void
    {
        $userId = 1;
        $friendId = 2;

        $user = Mockery::mock(User::class);
        $friend = Mockery::mock(User::class);
        $builder = Mockery::mock(Builder::class);
        $friendRequest = Mockery::mock(FriendRequest::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $friend->shouldReceive('getAttribute')->with('id')->andReturn($friendId);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('requester_id', $friendId)
            ->andReturnSelf();

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('addressee_id', $userId)
            ->andReturnSelf();

        $builder
            ->shouldReceive('exists')
            ->once()
            ->withNoArgs()
            ->andReturnTrue();

        $friendRequest->shouldReceive('query')->once()->withNoArgs()->andReturn($builder);

        $repoMock = Mockery::mock(FriendRequestRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->andReturn($friendRequest);

        /** @var \App\Contracts\Repositories\FriendRequestRepositoryInterface $repoMock */
        $result = $repoMock->reciprocalRequestExists($userId, $friendId);

        $this->assertTrue($result);

        $this->assertEquals(5, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can delete reciprocal friend requests.
     *
     * @return void
     */
    public function test_if_can_delete_reciprocal_friend_requests(): void
    {
        $userId = 1;
        $friendId = 2;

        $builder = Mockery::mock(Builder::class);
        $friendRequest = Mockery::mock(FriendRequest::class);

        $builder->shouldReceive('where')
            ->once()
            ->with(Mockery::on(function (callable $argument) {
                return $argument instanceof Closure;
            }))->andReturnSelf();

        $builder->shouldReceive('orWhere')
            ->once()
            ->with(Mockery::on(function (callable $argument) {
                return $argument instanceof Closure;
            }))->andReturnSelf();

        $friendRequest->shouldReceive('query')->once()->withNoArgs()->andReturn($builder);

        $builder->shouldReceive('delete')
            ->once()
            ->andReturn(1);

        $repoMock = Mockery::mock(FriendRequestRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->andReturn($friendRequest);

        /** @var \App\Contracts\Repositories\FriendRequestRepositoryInterface $repoMock */
        $repoMock->deleteReciprocalRequests($userId, $friendId);

        $this->assertEquals(5, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
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

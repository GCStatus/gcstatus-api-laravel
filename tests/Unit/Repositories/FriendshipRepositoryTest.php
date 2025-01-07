<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\Friendship;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\FriendshipRepository;
use App\Contracts\Repositories\FriendshipRepositoryInterface;

class FriendshipRepositoryTest extends TestCase
{
    /**
     * The friendship repository.
     *
     * @var \App\Contracts\Repositories\FriendshipRepositoryInterface
     */
    private FriendshipRepositoryInterface $friendshipRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->friendshipRepository = app(FriendshipRepositoryInterface::class);
    }

    /**
     * Test if FriendshipRepository uses the Friendship model correctly.
     *
     * @return void
     */
    public function test_friendship_repository_uses_friendship_request_model(): void
    {
        /** @var \App\Repositories\FriendshipRepository $friendshipRepository */
        $friendshipRepository = $this->friendshipRepository;

        $this->assertInstanceOf(Friendship::class, $friendshipRepository->model());
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

        $builder = Mockery::mock(Builder::class);
        $friendship = Mockery::mock(Friendship::class);

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('user_id', $userId)
            ->andReturnSelf();

        $builder
            ->shouldReceive('where')
            ->once()
            ->with('friend_id', $friendId)
            ->andReturnSelf();

        $builder
            ->shouldReceive('exists')
            ->once()
            ->withNoArgs()
            ->andReturnTrue();

        $friendship->shouldReceive('query')->once()->withNoArgs()->andReturn($builder);

        $repoMock = Mockery::mock(FriendshipRepository::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $repoMock->shouldReceive('model')
            ->once()
            ->andReturn($friendship);

        /** @var \App\Contracts\Repositories\FriendshipRepositoryInterface $repoMock */
        $result = $repoMock->friendshipExists($userId, $friendId);

        $this->assertTrue($result);

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

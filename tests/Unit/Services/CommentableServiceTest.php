<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Models\{Game, Commentable, User};
use App\Repositories\CommentableRepository;
use App\Exceptions\Commentable\CommentDoesntBelongsToUserException;
use App\Contracts\Repositories\CommentableRepositoryInterface;
use App\Contracts\Services\{
    AuthServiceInterface,
    CommentableServiceInterface,
    ReplyNotificationServiceInterface,
};

class CommentableServiceTest extends TestCase
{
    /**
     * The commentable repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $commentableRepository;

    /**
     * The auth service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $authService;

    /**
     * The replier notification service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $replyNotificationService;

    /**
     * The commentable service.
     *
     * @var \App\Contracts\Services\CommentableServiceInterface
     */
    private CommentableServiceInterface $commentableService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->authService = Mockery::mock(AuthServiceInterface::class);
        $this->commentableRepository = Mockery::mock(CommentableRepositoryInterface::class);
        $this->replyNotificationService = Mockery::mock(ReplyNotificationServiceInterface::class);

        $this->app->instance(AuthServiceInterface::class, $this->authService);
        $this->app->instance(CommentableRepositoryInterface::class, $this->commentableRepository);
        $this->app->instance(ReplyNotificationServiceInterface::class, $this->replyNotificationService);

        $this->commentableService = app(CommentableServiceInterface::class);
    }

    /**
     * Test if CommentableService uses the Commentable model correctly.
     *
     * @return void
     */
    public function test_commentable_repository_uses_commentable_model(): void
    {
        $this->app->instance(CommentableRepositoryInterface::class, app(CommentableRepository::class));

        /** @var \App\Services\CommentableService $commentableService */
        $commentableService = app(CommentableServiceInterface::class);

        $this->assertInstanceOf(CommentableRepository::class, $commentableService->repository());
    }

    /**
     * Test if can create a commentable.
     *
     * @return void
     */
    public function test_if_can_create_a_commentable(): void
    {
        $data = [
            'user_id' => 1,
            'commentable_id' => 1,
            'commentable_type' => Game::class,
        ];

        $commentable = Mockery::mock(Commentable::class);

        $commentable->shouldReceive('getAttribute')->with('parent')->andReturnNull();

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->withNoArgs()
            ->andReturn($data['user_id']);

        $this->commentableRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($commentable);

        $result = $this->commentableService->create($data);

        $this->assertSame($result, $commentable);
        $this->assertInstanceOf(Commentable::class, $result);

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can create a commentable and doesn't notify parent user if is own auth user.
     *
     * @return void
     */
    public function test_if_can_create_a_commentable_and_doesnt_notify_parent_user_if_is_own_auth_user(): void
    {
        $data = [
            'user_id' => 1,
            'commentable_id' => 1,
            'commentable_type' => Game::class,
        ];

        $parent = Mockery::mock(Commentable::class);
        $commentable = Mockery::mock(Commentable::class);

        $parent->shouldReceive('getAttribute')->with('user_id')->andReturn($data['user_id']);

        $commentable->shouldReceive('getAttribute')->with('parent')->andReturn($parent);

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->withNoArgs()
            ->andReturn($data['user_id']);

        $this->commentableRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($commentable);

        $result = $this->commentableService->create($data);

        $this->assertSame($result, $commentable);
        $this->assertInstanceOf(Commentable::class, $result);

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can create a parent commentable and notify parent user.
     *
     * @return void
     */
    public function test_if_can_create_a_parent_commentable_and_notify_parent_user(): void
    {
        $data = [
            'user_id' => 1,
            'commentable_id' => 1,
            'commentable_type' => Game::class,
        ];

        $replier = Mockery::mock(User::class);
        $receiver = Mockery::mock(User::class);
        $parent = Mockery::mock(Commentable::class);
        $commentable = Mockery::mock(Commentable::class);

        $parent->shouldReceive('getAttribute')->with('user_id')->andReturn(2);
        $parent->shouldReceive('getAttribute')->with('user')->andReturn($receiver);

        $commentable->shouldReceive('getAttribute')->with('user')->andReturn($replier);
        $commentable->shouldReceive('getAttribute')->with('parent')->andReturn($parent);

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->withNoArgs()
            ->andReturn($data['user_id']);

        $this->commentableRepository
            ->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($commentable);

        $this->replyNotificationService
            ->shouldReceive('notifyNewReply')
            ->once()
            ->with($receiver, $replier, $commentable)
            ->andReturnNull();

        $result = $this->commentableService->create($data);

        $this->assertSame($result, $commentable);
        $this->assertInstanceOf(Commentable::class, $result);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can't delete a comment if not comment owner.
     *
     * @return void
     */
    public function test_if_cant_delete_a_comment_if_not_comment_owner(): void
    {
        $userId = 1;

        $data = [
            'user_id' => 2,
            'commentable_id' => 1,
            'commentable_type' => Game::class,
        ];

        $commentable = Mockery::mock(Commentable::class);
        $commentable->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $commentable->shouldReceive('getAttribute')->with('user_id')->andReturn($data['user_id']);

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->withNoArgs()
            ->andReturn($userId);

        /** @var \App\Models\Commentable $commentable */
        $this->commentableRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($commentable->id)
            ->andReturn($commentable);

        $this->expectException(CommentDoesntBelongsToUserException::class);
        $this->expectExceptionMessage('This comment does not belongs to your user. No one action is allowed.');

        $this->commentableService->delete($commentable->id);
    }

    /**
     * Test if can delete a comment if is comment owner.
     *
     * @return void
     */
    public function test_if_can_delete_a_comment_if_is_comment_owner(): void
    {
        $userId = 1;

        $data = [
            'user_id' => $userId,
            'commentable_id' => 1,
            'commentable_type' => Game::class,
        ];

        $commentable = Mockery::mock(Commentable::class);
        $commentable->shouldAllowMockingProtectedMethods();
        $commentable->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $commentable->shouldReceive('getAttribute')->with('user_id')->andReturn($data['user_id']);

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->withNoArgs()
            ->andReturn($userId);

        $commentable
            ->shouldReceive('delete')
            ->once()
            ->withNoArgs()
            ->andReturnTrue();

        /** @var \App\Models\Commentable $commentable */
        $this->commentableRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($commentable->id)
            ->andReturn($commentable);

        $this->commentableService->delete($commentable->id);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }
}

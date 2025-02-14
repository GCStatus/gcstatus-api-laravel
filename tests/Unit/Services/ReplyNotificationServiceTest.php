<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\{User, Commentable, Game};
use App\Notifications\DatabaseNotification;
use App\Contracts\Services\ReplyNotificationServiceInterface;

class ReplyNotificationServiceTest extends TestCase
{
    /**
     * The reply notification service.
     *
     * @var \App\Contracts\Services\ReplyNotificationServiceInterface
     */
    private ReplyNotificationServiceInterface $replyNotificationService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->replyNotificationService = app(ReplyNotificationServiceInterface::class);
    }

    /**
     * Test if can notify a reply for given user comment.
     *
     * @return void
     */
    public function test_if_can_notify_a_reply_for_given_user_comment(): void
    {
        $game = Mockery::mock(Game::class);
        $replier = Mockery::mock(User::class);
        $receiver = Mockery::mock(User::class);
        $comment = Mockery::mock(Commentable::class);

        $receiver->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $replier->shouldReceive('getAttribute')->with('nickname')->andReturn(fake()->userName());

        $game->shouldReceive('getAttribute')->with('slug')->andReturn('fake-slug');

        $comment->shouldReceive('getAttribute')->with('commentable')->andReturn($game);
        $comment->shouldReceive('getAttribute')->with('commentable_type')->andReturn(Game::class);

        $receiver->shouldReceive('notify')
            ->once()
            ->with(Mockery::on(function (DatabaseNotification $notification) use ($replier, $game) {
                /** @var \App\Models\Game $game */
                /** @var \App\Models\User $replier */
                return $notification->data['icon'] === 'FaRegComment' &&
                    $notification->data['actionUrl'] === "/games/$game->slug" &&
                    $notification->data['title'] === "$replier->nickname just replied your comment.";
            }));

        /** @var \App\Models\User $receiver */
        /** @var \App\Models\User $replier */
        /** @var \App\Models\Commentable $comment */
        $this->replyNotificationService->notifyNewReply($receiver, $replier, $comment);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can generate the action url as pound if commentable type is untracked.
     *
     * @return void
     */
    public function test_if_can_generate_the_action_url_as_pound_if_commentable_type_is_untracked(): void
    {
        $game = Mockery::mock(Game::class);
        $replier = Mockery::mock(User::class);
        $receiver = Mockery::mock(User::class);
        $comment = Mockery::mock(Commentable::class);

        $receiver->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $replier->shouldReceive('getAttribute')->with('nickname')->andReturn(fake()->userName());

        $game->shouldReceive('getAttribute')->with('slug')->andReturn('fake-slug');

        $comment->shouldReceive('getAttribute')->with('commentable')->andReturn($game);
        $comment->shouldReceive('getAttribute')->with('commentable_type')->andReturn('untracked');

        $receiver->shouldReceive('notify')
            ->once()
            ->with(Mockery::on(function (DatabaseNotification $notification) use ($replier) {
                /** @var \App\Models\User $replier */
                return $notification->data['icon'] === 'FaRegComment' &&
                    $notification->data['actionUrl'] === "#" &&
                    $notification->data['title'] === "$replier->nickname just replied your comment.";
            }));

        /** @var \App\Models\User $receiver */
        /** @var \App\Models\User $replier */
        /** @var \App\Models\Commentable $comment */
        $this->replyNotificationService->notifyNewReply($receiver, $replier, $comment);

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

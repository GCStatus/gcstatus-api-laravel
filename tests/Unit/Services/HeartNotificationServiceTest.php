<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use ReflectionMethod;
use InvalidArgumentException;
use App\Notifications\DatabaseNotification;
use App\Models\{User, Heartable, Commentable, Game};
use App\Contracts\Services\HeartNotificationServiceInterface;

class HeartNotificationServiceTest extends TestCase
{
    /**
     * The heart notification service.
     *
     * @var \App\Contracts\Services\HeartNotificationServiceInterface
     */
    private HeartNotificationServiceInterface $heartNotificationService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->heartNotificationService = app(HeartNotificationServiceInterface::class);
    }

    /**
     * Test if it sends notification when valid heartable is given.
     *
     * @return void
     */
    public function test_if_it_sends_notification_when_valid_heartable_is_given(): void
    {
        $user = Mockery::mock(User::class);
        $hearter = Mockery::mock(User::class);
        $game = Mockery::mock(Game::class);

        $fakeSlug = fake()->slug();
        $fakeUsername = fake()->userName();

        $game->shouldReceive('getAttribute')->with('slug')->andReturn($fakeSlug);

        $user->shouldReceive('getKey')->andReturn(1);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $hearter->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $hearter->shouldReceive('getAttribute')->with('nickname')->andReturn($fakeUsername);

        $commentable = Mockery::mock(Commentable::class);
        $commentable->shouldReceive('getAttribute')->with('user_id')->andReturn(1);
        $commentable->shouldReceive('getAttribute')->with('user')->andReturn($user);
        $commentable->shouldReceive('getAttribute')->with('commentable')->andReturn($game);
        $commentable->shouldReceive('getAttribute')->with('commentable_type')->andReturn(Game::class);

        $heartable = Mockery::mock(Heartable::class);
        $heartable->shouldReceive('getAttribute')->with('heartable_type')->andReturn(Commentable::class);
        $heartable->shouldReceive('getAttribute')->with('heartable')->andReturn($commentable);
        $heartable->shouldReceive('getAttribute')->with('user')->andReturn($hearter);

        $user->shouldReceive('notify')
            ->once()
            ->with(Mockery::on(function (DatabaseNotification $notification) use ($hearter, $game) {
                /** @var \App\Models\Game $game */
                /** @var \App\Models\User $hearter */
                return $notification->data['actionUrl'] === "/games/$game->slug" &&
                    $notification->data['title'] === "$hearter->nickname hearted your comment!" &&
                    $notification->data['icon'] === 'IoIosHeartEmpty';
            }));

        /** @var \App\Models\Heartable $heartable */
        $this->heartNotificationService->notifyNewHeart($heartable);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if it doesn't send notification if user_hearts_their_own_comment.
     *
     * @return void
     */
    public function test_if_it_does_not_send_notification_if_user_hearts_their_own_comment(): void
    {
        $user = Mockery::mock(User::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $user->shouldNotReceive('notify');

        /** @var \App\Models\User $user */
        $commentable = Mockery::mock(Commentable::class);
        $commentable->shouldReceive('getAttribute')->with('user')->andReturn($user);
        $commentable->shouldReceive('getAttribute')->with('user_id')->andReturn($user->id);
        $commentable->shouldReceive('getAttribute')->with('commentable_type')->andReturn(Game::class);

        $heartable = Mockery::mock(Heartable::class);
        $heartable->shouldReceive('getAttribute')->with('heartable_type')->andReturn(Commentable::class);
        $heartable->shouldReceive('getAttribute')->with('heartable')->andReturn($commentable);
        $heartable->shouldReceive('getAttribute')->with('user')->andReturn($user);

        /** @var \App\Models\Heartable $heartable */
        $this->heartNotificationService->notifyNewHeart($heartable);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if it throws exception for invalid heartable type.
     *
     * @return void
     */
    public function test_if_it_throws_exception_for_invalid_heartable_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid heartable type for notification.');

        $user = Mockery::mock(User::class);
        $hearter = Mockery::mock(User::class);
        $commentable = Mockery::mock(Commentable::class);
        $heartable = Mockery::mock(Heartable::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $hearter->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $hearter->shouldReceive('getAttribute')->with('nickname')->andReturn(fake()->userName());

        /** @var \App\Models\User $user */
        $commentable->shouldReceive('getAttribute')->with('user')->andReturn($user);
        $commentable->shouldReceive('getAttribute')->with('user_id')->andReturn($user->id);
        $commentable->shouldReceive('getAttribute')->with('commentable_type')->andReturn('invalidType');

        $heartable->shouldReceive('getAttribute')->with('heartable_type')->andReturn('invalidType');
        $heartable->shouldReceive('getAttribute')->with('heartable')->andReturn($commentable);
        $heartable->shouldReceive('getAttribute')->with('user')->andReturn($hearter);

        $reflectionMethod = new ReflectionMethod($this->heartNotificationService, 'getNotifiable');
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invoke($this->heartNotificationService, $heartable);
    }

    /**
     * Test if it throws exception when getting notification with invalid heartable_type.
     *
     * @return void
     */
    public function test_if_it_throws_exception_when_getting_notification_with_invalid_heartable_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid heartable type for notification.');

        $user = Mockery::mock(User::class);
        $hearter = Mockery::mock(User::class);
        $commentable = Mockery::mock(Commentable::class);
        $heartable = Mockery::mock(Heartable::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $hearter->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $hearter->shouldReceive('getAttribute')->with('nickname')->andReturn(fake()->userName());

        /** @var \App\Models\User $user */
        $commentable->shouldReceive('getAttribute')->with('user')->andReturn($user);
        $commentable->shouldReceive('getAttribute')->with('user_id')->andReturn($user->id);
        $commentable->shouldReceive('getAttribute')->with('commentable_type')->andReturn('invalidType');

        $heartable->shouldReceive('getAttribute')->with('heartable_type')->andReturn('invalidType');
        $heartable->shouldReceive('getAttribute')->with('heartable')->andReturn($commentable);
        $heartable->shouldReceive('getAttribute')->with('user')->andReturn($hearter);

        $reflectionMethod = new ReflectionMethod($this->heartNotificationService, 'getNotification');
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invoke($this->heartNotificationService, $heartable, $user);
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

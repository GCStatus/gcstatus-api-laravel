<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\{Level, User};
use App\Notifications\DatabaseNotification;
use App\Contracts\Services\LevelNotificationServiceInterface;

class LevelNotificationServiceTest extends TestCase
{
    /**
     * The level notification service.
     *
     * @var \App\Contracts\Services\LevelNotificationServiceInterface
     */
    private LevelNotificationServiceInterface $levelNotificationService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->levelNotificationService = app(LevelNotificationServiceInterface::class);
    }

    /**
     * Test if can notify a gained experience for given user.
     *
     * @return void
     */
    public function test_if_can_notify_a_gained_experience_for_given_user(): void
    {
        $amount = 50;

        $user = Mockery::mock(User::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $user->shouldReceive('notify')
            ->once()
            ->with(Mockery::on(function (DatabaseNotification $notification) use ($user, $amount) {
                /** @var \App\Models\User $user */
                return $notification->data['userId'] === (string)$user->id &&
                    $notification->data['actionUrl'] === '/profile' &&
                    $notification->data['title'] === "You received $amount experience.";
            }));

        /** @var \App\Models\User $user */
        $this->levelNotificationService->notifyExperienceGained($user, $amount);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can notify a level up for given user.
     *
     * @return void
     */
    public function test_if_can_notify_a_level_up_for_given_user(): void
    {
        $user = Mockery::mock(User::class);
        $level = Mockery::mock(Level::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $level->shouldReceive('getAttribute')->with('level')->andReturn(2);

        $user->shouldReceive('notify')
            ->once()
            ->with(Mockery::on(function (DatabaseNotification $notification) use ($user, $level) {
                /** @var \App\Models\User $user */
                /** @var \App\Models\Level $level */
                return $notification->data['userId'] === (string)$user->id &&
                    $notification->data['actionUrl'] === '/profile/?section=levels' &&
                    $notification->data['title'] === "Congratulations for reaching a new level! You are now on Level {$level->level}.";
            }));

        /** @var \App\Models\User $user */
        /** @var \App\Models\Level $level */
        $this->levelNotificationService->notifyLevelUp($user, $level);

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

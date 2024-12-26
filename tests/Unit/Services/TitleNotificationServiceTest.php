<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\{Title, User};
use App\Notifications\DatabaseNotification;
use App\Contracts\Services\TitleNotificationServiceInterface;

class TitleNotificationServiceTest extends TestCase
{
    /**
     * The transaction notification service.
     *
     * @var \App\Contracts\Services\TitleNotificationServiceInterface
     */
    private TitleNotificationServiceInterface $titleNotificationService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->titleNotificationService = app(TitleNotificationServiceInterface::class);
    }

    /**
     * Test if can notify a new title for given user.
     *
     * @return void
     */
    public function test_if_can_notify_a_new_title_for_given_user(): void
    {
        $user = Mockery::mock(User::class);
        $title = Mockery::mock(Title::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $title->shouldReceive('getAttribute')->with('id')->andReturn(100);

        $user->shouldReceive('notify')
            ->once()
            ->with(Mockery::on(function (DatabaseNotification $notification) use ($user, $title) {
                /** @var \App\Models\User $user */
                /** @var \App\Models\Title $title */
                return $notification->data['userId'] === (string)$user->id &&
                    $notification->data['actionUrl'] === "/profile/?section=titles&id={$title->id}" &&
                    $notification->data['title'] === 'You earned a new title!';
            }));

        /** @var \App\Models\User $user */
        /** @var \App\Models\Title $title */
        $this->titleNotificationService->notifyNewTitle($user, $title);

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

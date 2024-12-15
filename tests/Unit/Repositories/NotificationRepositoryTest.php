<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Notifications\DatabaseNotificationCollection;
use App\Contracts\Repositories\NotificationRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class NotificationRepositoryTest extends TestCase
{
    /**
     * The notification repository.
     *
     * @var \App\Contracts\Repositories\NotificationRepositoryInterface
     */
    private NotificationRepositoryInterface $notificationRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->notificationRepository = app(NotificationRepositoryInterface::class);
    }

    /**
     * Test if can get all user notifications.
     *
     * @return void
     */
    public function test_if_can_get_all_user_notifications(): void
    {
        $user = Mockery::mock(User::class);
        $builder = Mockery::mock(Builder::class);
        $notification = Mockery::mock(DatabaseNotification::class);

        $notificationCollection = Collection::make([$notification]);

        $user
            ->shouldReceive('notifications')
            ->once()
            ->withNoArgs()
            ->andReturn($builder);

        $builder
            ->shouldReceive('get')
            ->once()
            ->withNoArgs()
            ->andReturn($notificationCollection);

        /** @var \App\Models\User $user */
        $result = $this->notificationRepository->all($user);

        $this->assertInstanceOf(Collection::class, $result);

        $this->assertSame($result, $notificationCollection);
    }

    /**
     * Test if can find notification by id.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function test_if_can_find_notification_by_id(): void
    {
        $id = fake()->uuid();

        $notification = Mockery::mock('overload:' . DatabaseNotification::class);
        $notification->shouldReceive('findOrFail')->once()->with($id)->andReturnSelf();

        $result = $this->notificationRepository->findOrFail($id);

        $this->assertSame($result, $notification);
    }

    /**
     * Test if can fail notification by id.
     *
     * @return void
     */
    #[RunInSeparateProcess]
    public function test_if_can_fail_notification_by_id(): void
    {
        $id = fake()->uuid();

        $notification = Mockery::mock('overload:' . DatabaseNotification::class);
        $notification->shouldReceive('findOrFail')->once()->with($id)->andThrow(new ModelNotFoundException());

        $this->expectException(ModelNotFoundException::class);

        $this->notificationRepository->findOrFail($id);
    }

    /**
     * Test if can mark notification as read.
     *
     * @return void
     */
    public function test_if_can_mark_notification_as_read(): void
    {
        $notification = Mockery::mock(DatabaseNotification::class);
        $notification->shouldReceive('markAsRead')->once();

        /** @var \Illuminate\Notifications\DatabaseNotification $notification */
        $this->notificationRepository->markAsRead($notification);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can mark notification as unread.
     *
     * @return void
     */
    public function test_if_can_mark_notification_as_unread(): void
    {
        $notification = Mockery::mock(DatabaseNotification::class);
        $notification->shouldReceive('markAsUnread')->once();

        /** @var \Illuminate\Notifications\DatabaseNotification $notification */
        $this->notificationRepository->markAsUnread($notification);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can mark all notifications as read.
     *
     * @return void
     */
    public function test_if_can_mark_all_notifications_as_read(): void
    {
        $user = Mockery::mock(User::class);
        $builder = Mockery::mock(Builder::class);

        $notifications = Mockery::mock(DatabaseNotificationCollection::class);
        $notifications->shouldReceive('markAsRead')->once();

        $user
            ->shouldReceive('notifications')
            ->once()
            ->withNoArgs()
            ->andReturn($builder);

        $builder
            ->shouldReceive('get')
            ->once()
            ->withNoArgs()
            ->andReturn($notifications);

        /** @var \App\Models\User $user */
        $this->notificationRepository->markAllAsRead($user);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can remove a notification.
     *
     * @return void
     */
    public function test_if_can_remove_a_notification(): void
    {
        $notification = Mockery::mock(DatabaseNotification::class);
        $notification->shouldReceive('delete')->once();

        /** @var \Illuminate\Notifications\DatabaseNotification $notification */
        $this->notificationRepository->remove($notification);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can remove all notifications for given user.
     *
     * @return void
     */
    public function test_if_can_remove_all_notifications_for_given_user(): void
    {
        $user = Mockery::mock(User::class);
        $builder = Mockery::mock(Builder::class);

        $notificationMock = Mockery::mock(DatabaseNotificationCollection::class);

        $notificationMock->shouldReceive('each')
            ->once()
            ->with(Mockery::on(function ($callback) {
                $mockNotification = Mockery::mock(DatabaseNotification::class);
                $mockNotification->shouldReceive('delete')->once();
                $callback($mockNotification);
                return true;
            }));

        $user
            ->shouldReceive('notifications')
            ->once()
            ->withNoArgs()
            ->andReturn($builder);

        $builder
            ->shouldReceive('get')
            ->once()
            ->withNoArgs()
            ->andReturn($notificationMock);

        /** @var \App\Models\User $user */
        $this->notificationRepository->removeAll($user);

        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Cleanup Mockery after each test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}

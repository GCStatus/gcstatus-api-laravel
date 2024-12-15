<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;
use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Exceptions\Notification\NotificationDoesntBelongsToUserException;
use App\Contracts\Services\{
    AuthServiceInterface,
    NotificationServiceInterface,
};

class NotificationServiceTest extends TestCase
{
    /**
     * The notification repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $notificationRepository;

    /**
     * The auth service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $authService;

    /**
     * The notification service.
     *
     * @var \App\Contracts\Services\NotificationServiceInterface
     */
    private NotificationServiceInterface $notificationService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->authService = Mockery::mock(AuthServiceInterface::class);
        $this->notificationRepository = Mockery::mock(NotificationRepositoryInterface::class);

        $this->app->instance(AuthServiceInterface::class, $this->authService);
        $this->app->instance(NotificationRepositoryInterface::class, $this->notificationRepository);

        $this->notificationService = app(NotificationServiceInterface::class);
    }

    /**
     * Test if can get all notifications.
     *
     * @return void
     */
    public function test_if_can_get_all_notifications(): void
    {
        $user = Mockery::mock(User::class);
        $notification = Mockery::mock(DatabaseNotification::class);

        $notificationCollection = Collection::make([$notification]);

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->andReturn($user);

        $this->notificationRepository
            ->shouldReceive('all')
            ->once()
            ->with($user)
            ->andReturn($notificationCollection);

        $result = $this->notificationService->all();

        $this->assertSame($result, $notificationCollection);
    }

    /**
     * Test if can mark a notification as read.
     *
     * @return void
     */
    public function test_if_can_mark_a_notification_as_read(): void
    {
        $userId = 1;
        $notificationId = fake()->uuid();

        $user = Mockery::mock(User::class);
        $notification = Mockery::mock(DatabaseNotification::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $notification->shouldReceive('getAttribute')->with('id')->andReturn($notificationId);
        $notification->shouldReceive('getAttribute')->with('notifiable_id')->andReturn($userId);

        $this->notificationRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($notificationId)
            ->andReturn($notification);

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->andReturn($userId);

        $this->notificationRepository
            ->shouldReceive('markAsRead')
            ->once()
            ->with($notification);

        $this->notificationService->markAsRead($notificationId);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can't mark a notification as read if notification doesn't belongs to user.
     *
     * @return void
     */
    public function test_if_cant_mark_a_notification_as_read_if_notification_doesnt_belongs_to_user(): void
    {
        $userId = 1;
        $notificationId = fake()->uuid();

        $user = Mockery::mock(User::class);
        $notification = Mockery::mock(DatabaseNotification::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $notification->shouldReceive('getAttribute')->with('notifiable_id')->andReturn(2);
        $notification->shouldReceive('getAttribute')->with('id')->andReturn($notificationId);

        $this->notificationRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($notificationId)
            ->andReturn($notification);

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->andReturn($userId);

        $this->notificationRepository->shouldNotReceive('markAsRead');

        $this->expectException(NotificationDoesntBelongsToUserException::class);
        $this->expectExceptionMessage('The given notification does not belongs to your user. This action is unauthorized!');

        $this->notificationService->markAsRead($notificationId);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can mark a notification as unread.
     *
     * @return void
     */
    public function test_if_can_mark_a_notification_as_unread(): void
    {
        $userId = 1;
        $notificationId = fake()->uuid();

        $user = Mockery::mock(User::class);
        $notification = Mockery::mock(DatabaseNotification::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $notification->shouldReceive('getAttribute')->with('id')->andReturn($notificationId);
        $notification->shouldReceive('getAttribute')->with('notifiable_id')->andReturn($userId);

        $this->notificationRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($notificationId)
            ->andReturn($notification);

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->andReturn($userId);

        $this->notificationRepository
            ->shouldReceive('markAsUnread')
            ->once()
            ->with($notification);

        $this->notificationService->markAsUnread($notificationId);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can't mark a notification as unread if notification doesn't belongs to user.
     *
     * @return void
     */
    public function test_if_cant_mark_a_notification_as_unread_if_notification_doesnt_belongs_to_user(): void
    {
        $userId = 1;
        $notificationId = fake()->uuid();

        $user = Mockery::mock(User::class);
        $notification = Mockery::mock(DatabaseNotification::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $notification->shouldReceive('getAttribute')->with('notifiable_id')->andReturn(2);
        $notification->shouldReceive('getAttribute')->with('id')->andReturn($notificationId);

        $this->notificationRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($notificationId)
            ->andReturn($notification);

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->andReturn($userId);

        $this->notificationRepository->shouldNotReceive('markAsUnread');

        $this->expectException(NotificationDoesntBelongsToUserException::class);
        $this->expectExceptionMessage('The given notification does not belongs to your user. This action is unauthorized!');

        $this->notificationService->markAsUnread($notificationId);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can mark all notification as read.
     *
     * @return void
     */
    public function test_if_can_mark_all_notification_as_read(): void
    {
        $user = Mockery::mock(User::class);

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->andReturn($user);

        $this->notificationRepository
            ->shouldReceive('markAllAsRead')
            ->once()
            ->with($user);

        $this->notificationService->markAllAsRead();

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can remove a notification.
     *
     * @return void
     */
    public function test_if_can_remove_a_notification(): void
    {
        $userId = 1;
        $notificationId = fake()->uuid();

        $user = Mockery::mock(User::class);
        $notification = Mockery::mock(DatabaseNotification::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $notification->shouldReceive('getAttribute')->with('id')->andReturn($notificationId);
        $notification->shouldReceive('getAttribute')->with('notifiable_id')->andReturn($userId);

        $this->notificationRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($notificationId)
            ->andReturn($notification);

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->andReturn($userId);

        $this->notificationRepository
            ->shouldReceive('remove')
            ->once()
            ->with($notification);

        $this->notificationService->remove($notificationId);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can't remove a notification if notification doesn't belongs to user.
     *
     * @return void
     */
    public function test_if_cant_remove_a_notification_if_notification_doesnt_belongs_to_user(): void
    {
        $userId = 1;
        $notificationId = fake()->uuid();

        $user = Mockery::mock(User::class);
        $notification = Mockery::mock(DatabaseNotification::class);

        $user->shouldReceive('getAttribute')->with('id')->andReturn($userId);
        $notification->shouldReceive('getAttribute')->with('notifiable_id')->andReturn(2);
        $notification->shouldReceive('getAttribute')->with('id')->andReturn($notificationId);

        $this->notificationRepository
            ->shouldReceive('findOrFail')
            ->once()
            ->with($notificationId)
            ->andReturn($notification);

        $this->authService
            ->shouldReceive('getAuthId')
            ->once()
            ->andReturn($userId);

        $this->notificationRepository->shouldNotReceive('remove');

        $this->expectException(NotificationDoesntBelongsToUserException::class);
        $this->expectExceptionMessage('The given notification does not belongs to your user. This action is unauthorized!');

        $this->notificationService->remove($notificationId);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Test if can remove all notifications.
     *
     * @return void
     */
    public function test_if_can_remove_all_notifications(): void
    {
        $user = Mockery::mock(User::class);

        $this->authService
            ->shouldReceive('getAuthUser')
            ->once()
            ->andReturn($user);

        $this->notificationRepository
            ->shouldReceive('removeAll')
            ->once()
            ->with($user);

        $this->notificationService->removeAll();

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations met.');
    }

    /**
     * Tear down test environment.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}

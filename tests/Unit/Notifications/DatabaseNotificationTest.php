<?php

namespace Tests\Unit\Notifications;

use Mockery;
use stdClass;
use Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Queue;
use App\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Notification;

class DatabaseNotificationTest extends TestCase
{
    /**
     * Test if via returns correct channels.
     *
     * @return void
     */
    public function test_if_via_returns_correct_channels(): void
    {
        $data = [
            'userId' => '1',
            'icon' => fake()->word(),
            'actionUrl' => fake()->url(),
            'title' => fake()->realText(),
        ];

        $notification = new DatabaseNotification($data);

        $notifiable = Mockery::mock(stdClass::class);

        $channels = $notification->via($notifiable);

        $this->assertEquals(['database', 'broadcast'], $channels);
    }

    /**
     * Test if toArray returns correct data.
     *
     * @return void
     */
    public function test_toArray_returns_correct_data(): void
    {
        $data = [
            'icon' => fake()->word(),
            'actionUrl' => fake()->url(),
            'title' => fake()->realText(),
        ];

        $notification = new DatabaseNotification($data);

        $notifiable = Mockery::mock(stdClass::class);

        $result = $notification->toArray($notifiable);

        $this->assertEquals($data, $result);
    }

    /**
     * Test if toBroadcast returns correct structure.
     *
     * @return void
     */
    public function test_if_toBroadcast_returns_correct_structure(): void
    {
        $data = [
            'userId' => '1',
            'icon' => fake()->word(),
            'actionUrl' => fake()->url(),
            'title' => fake()->realText(),
        ];

        $notification = new DatabaseNotification($data);

        $notifiable = Mockery::mock(stdClass::class);

        Carbon::setTestNow($now = Carbon::create(2023, 12, 1, 12, 0, 0));

        $result = $notification->toBroadcast($notifiable);

        $expected = [
            'data' => $data,
            'extra' => [
                'notification_id' => null,
                'timestamp' => $now?->toISOString(),
            ],
        ];

        $this->assertEquals($expected, $result);

        Carbon::setTestNow();
    }

    /**
     * Test if broadcastAs returns correct event name.
     *
     * @return void
     */
    public function test_if_broadcastAs_returns_correct_event_name(): void
    {
        $data = [
            'userId' => '1',
            'icon' => fake()->word(),
            'actionUrl' => fake()->url(),
            'title' => fake()->realText(),
        ];

        $notification = new DatabaseNotification($data);

        $result = $notification->broadcastAs();

        $this->assertEquals('notification.created', $result);
    }

    /**
     * Test if broadcastOn returns correct channel.
     *
     * @return void
     */
    public function test_if_broadcastOn_returns_correct_channel(): void
    {
        $userId = '123';

        $data = [
            'userId' => $userId,
            'icon' => fake()->word(),
            'actionUrl' => fake()->url(),
            'title' => fake()->realText(),
        ];

        $notification = new DatabaseNotification($data);

        $result = $notification->broadcastOn();

        $this->assertInstanceOf(Channel::class, $result[0]);

        $this->assertEquals("App.Models.User.$userId", $result[0]->name);
    }

    /**
     * Test if database notification can be dispatched.
     *
     * @return void
     */
    public function test_if_database_notification_can_be_dispatched(): void
    {
        Notification::fake();

        $data = [
            'userId' => '1',
            'icon' => fake()->word(),
            'actionUrl' => fake()->url(),
            'title' => fake()->realText(),
        ];

        $notifiable = new class () {
            /**
             * Get the notifiable key.
             *
             * @return int
             */
            public function getKey(): int
            {
                return 1;
            }
        };

        $notification = new DatabaseNotification($data);

        Notification::send($notifiable, $notification);

        Notification::assertSentTo(
            [$notifiable],
            DatabaseNotification::class,
            function (DatabaseNotification $notificationInstance, array $channels) use ($data) {
                return $notificationInstance->data === $data && in_array('database', $channels);
            }
        );
    }

    /**
     * Test if database notification can be queued.
     *
     * @return void
     */
    public function test_if_database_notification_can_be_queued(): void
    {
        Queue::fake();

        $data = [
            'userId' => '1',
            'icon' => fake()->word(),
            'actionUrl' => fake()->url(),
            'title' => fake()->realText(),
        ];

        $notification = new DatabaseNotification($data);

        Queue::push($notification);

        Queue::assertPushed(DatabaseNotification::class, function (DatabaseNotification $job) use ($notification) {
            return $job->data === $notification->data;
        });
    }

    /**
     * Tear down test environments.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}

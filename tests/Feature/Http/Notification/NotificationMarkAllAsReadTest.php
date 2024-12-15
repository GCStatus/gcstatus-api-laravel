<?php

namespace Tests\Feature\Http\Notification;

use App\Models\User;
use Tests\Traits\HasDummyNotification;
use Tests\Feature\Http\BaseIntegrationTesting;

class NotificationMarkAllAsReadTest extends BaseIntegrationTesting
{
    use HasDummyNotification;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser();
    }

    /**
     * Test if can't mark all notifications as read if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_mark_all_notifications_as_read_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->putJson(route('notifications.mark-all-as-read'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can mark all notifications as read.
     *
     * @return void
     */
    public function test_if_can_mark_all_notifications_as_read(): void
    {
        $this->putJson(route('notifications.mark-all-as-read'))->assertOk();
    }

    /**
     * Test if can mark all user notifications as read on read_at attribute.
     *
     * @return void
     */
    public function test_if_can_mark_all_user_notifications_as_read_on_read_at_attribute(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->createDummyNotificationTo($this->user);
        }

        foreach ($this->user->notifications()->get() as $notification) {
            $this->assertNull($notification->read_at);
        }

        $this->putJson(route('notifications.mark-all-as-read'))->assertOk();

        foreach ($this->user->notifications()->get() as $notification) {
            $this->assertNotNull($notification->read_at);
        }
    }
}

<?php

namespace Tests\Feature\Http\Notification;

use App\Models\User;
use Tests\Traits\HasDummyNotification;
use Tests\Feature\Http\BaseIntegrationTesting;
use Illuminate\Notifications\DatabaseNotification;

class NotificationMarkAsUnreadTest extends BaseIntegrationTesting
{
    use HasDummyNotification;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy notification.
     *
     * @var \Illuminate\Notifications\DatabaseNotification
     */
    private DatabaseNotification $notification;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser();
        $this->createDummyNotificationTo($this->user);

        /** @var \Illuminate\Notifications\DatabaseNotification $notification */
        $notification = $this->user->notifications[0];

        $this->notification = $notification;

        $this->notification->markAsRead();
    }

    /**
     * Test if can't mark a notification as unread if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_mark_a_notification_as_unread_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->putJson(route('notifications.mark-as-unread', $this->notification))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can throw not found if notification doesn't exist.
     *
     * @return void
     */
    public function test_if_can_throw_not_found_if_notification_doesnt_exist(): void
    {
        $id = 1239801;

        $expectedMsg = "No query results for model [Illuminate\\\\Notifications\\\\DatabaseNotification] $id";

        $this->putJson(route('notifications.mark-as-unread', $id))
            ->assertNotFound()
            ->assertSee($expectedMsg);
    }

    /**
     * Test if can't mark a notification as unread if authenticated user is not the notification owner.
     *
     * @return void
     */
    public function test_if_cant_mark_a_notification_as_unread_if_authenticated_user_is_not_the_notification_owner(): void
    {
        $this->createDummyNotificationTo($another = $this->createDummyUser());

        $notification = DatabaseNotification::where('notifiable_id', $another->id)->firstOrFail();

        $this->putJson(route('notifications.mark-as-unread', $notification))
            ->assertForbidden()
            ->assertSee('The given notification does not belongs to your user. This action is unauthorized!');
    }

    /**
     * Test if can mark the notification as unread.
     *
     * @return void
     */
    public function test_if_can_mark_the_notification_as_unread(): void
    {
        $this->putJson(route('notifications.mark-as-unread', $this->notification))->assertOk();
    }

    /**
     * Test if can mark notification as unread on attribute.
     *
     * @return void
     */
    public function test_if_can_mark_notification_as_unread_on_attribute(): void
    {
        $this->assertNotNull($this->notification->read_at);

        $this->putJson(route('notifications.mark-as-unread', $this->notification))->assertOk();

        $this->notification->refresh();

        $this->assertNull($this->notification->read_at);
    }
}

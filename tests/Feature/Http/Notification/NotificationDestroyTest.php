<?php

namespace Tests\Feature\Http\Notification;

use App\Models\User;
use Tests\Traits\HasDummyNotification;
use Tests\Feature\Http\BaseIntegrationTesting;
use Illuminate\Notifications\DatabaseNotification;

class NotificationDestroyTest extends BaseIntegrationTesting
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
    }

    /**
     * Test if can't remove a notification if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_remove_a_notification_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->deleteJson(route('notifications.destroy', $this->notification))
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

        $this->deleteJson(route('notifications.destroy', $id))
            ->assertNotFound()
            ->assertSee($expectedMsg);
    }

    /**
     * Test if can't remove a notification if authenticated user is not the notification owner.
     *
     * @return void
     */
    public function test_if_cant_remove_a_notification_if_authenticated_user_is_not_the_notification_owner(): void
    {
        $this->createDummyNotificationTo($another = $this->createDummyUser());

        $notification = DatabaseNotification::where('notifiable_id', $another->id)->firstOrFail();

        $this->deleteJson(route('notifications.destroy', $notification))
            ->assertForbidden()
            ->assertSee('The given notification does not belongs to your user. This action is unauthorized!');
    }

    /**
     * Test if can remove the notification.
     *
     * @return void
     */
    public function test_if_can_remove_the_notification(): void
    {
        $this->deleteJson(route('notifications.destroy', $this->notification))->assertOk();
    }

    /**
     * Test if can exclude notification from database on removal.
     *
     * @return void
     */
    public function test_if_can_exclude_the_notification_from_database_on_removal(): void
    {
        $this->assertDatabaseHas('notifications', [
            'id' => $this->notification->id,
        ]);

        $this->deleteJson(route('notifications.destroy', $this->notification))->assertOk();

        $this->assertDatabaseMissing('notifications', [
            'id' => $this->notification->id,
        ]);
    }
}

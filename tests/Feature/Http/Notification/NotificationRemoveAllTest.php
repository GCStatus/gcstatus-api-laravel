<?php

namespace Tests\Feature\Http\Notification;

use App\Models\User;
use Tests\Traits\HasDummyNotification;
use Tests\Feature\Http\BaseIntegrationTesting;

class NotificationRemoveAllTest extends BaseIntegrationTesting
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
     * Test if can't remove all notifications if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_remove_all_notifications_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->deleteJson(route('notifications.remove-all'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can remove all notifications.
     *
     * @return void
     */
    public function test_if_can_remove_all_notifications(): void
    {
        $this->deleteJson(route('notifications.remove-all'))->assertOk();
    }

    /**
     * Test if can remove all user notifications from database.
     *
     * @return void
     */
    public function test_if_can_remove_all_user_notifications_from_database(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $this->createDummyNotificationTo($this->user);
        }

        foreach ($this->user->notifications()->get() as $notification) {
            $this->assertDatabaseHas('notifications', [
                'id' => $notification->id,
            ]);
        }

        $this->deleteJson(route('notifications.remove-all'))->assertOk();

        foreach ($this->user->notifications()->get() as $notification) {
            $this->assertDatabaseMissing('notifications', [
                'id' => $notification->id,
            ]);
        }
    }
}

<?php

namespace Tests\Feature\Http\Notification;

use App\Models\User;
use Tests\Traits\HasDummyNotification;
use Tests\Feature\Http\BaseIntegrationTesting;

class NotificationIndexTest extends BaseIntegrationTesting
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
     * Test if can't get my notification if I'm not authenticated.
     *
     * @return void
     */
    public function test_if_cant_get_my_notifications_if_im_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('notifications.index'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can get notifications route.
     *
     * @return void
     */
    public function test_if_can_get_notifications_route(): void
    {
        $this->getJson(route('notifications.index'))->assertOk();
    }

    /**
     * Test if can get correct json notifications count.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_notifications_count(): void
    {
        $this->getJson(route('notifications.index'))->assertOk()->assertJsonCount(0, 'data');

        $this->createDummyNotificationTo($this->user);

        $this->getJson(route('notifications.index'))->assertOk()->assertJsonCount(1, 'data');

        $this->createDummyNotificationTo($this->createDummyUser());

        $this->getJson(route('notifications.index'))->assertOk()->assertJsonCount(1, 'data');
    }

    /**
     * Test if can get correct json structure.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure(): void
    {
        $this->createDummyNotificationTo($this->user);

        $this->getJson(route('notifications.index'))->assertOk()->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'data' => [],
                    'read_at',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    /**
     * Test if can get correct json data.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_data(): void
    {
        $this->createDummyNotificationTo($this->user);

        /** @var \Illuminate\Notifications\DatabaseNotification $notification */
        $notification = $this->user->notifications[0];

        $this->getJson(route('notifications.index'))->assertOk()->assertJson([
            'data' => [
                [
                    'id' => $notification->id,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at?->toISOString(),
                    'created_at' => $notification->created_at?->toISOString(),
                    'updated_at' => $notification->updated_at?->toISOString(),
                ],
            ],
        ]);
    }
}

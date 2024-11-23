<?php

namespace Tests\Unit\Notifications;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Notifications\QueuedResetPassword;
use Illuminate\Support\Facades\Notification;

class QueuedResetPasswordTest extends TestCase
{
    /**
     * Test if reset password notification is sent.
     *
     * @return void
     */
    public function test_if_can_send_reset_password_notification(): void
    {
        Notification::fake();

        $user = Mockery::mock(User::class)->makePartial();

        $token = 'dummy-reset-token';

        /** @var \App\Models\User $user */
        $user->notify(new QueuedResetPassword($token));

        Notification::assertSentTo(
            $user,
            QueuedResetPassword::class,
            function (QueuedResetPassword $notification) use ($token) {
                return $notification->token === $token;
            }
        );
    }

    /**
     * Test if content of reset password notification is correct.
     *
     * @return void
     */
    public function test_if_content_of_reset_password_notification_is_correct(): void
    {
        Notification::fake();

        $user = Mockery::mock(User::class)->makePartial();

        $token = 'dummy-reset-token';

        /** @var \App\Models\User $user */
        $user->notify(new QueuedResetPassword($token));

        Notification::assertSentTo(
            $user,
            QueuedResetPassword::class,
            function ($notification) use ($token, $user) {
                $url = $notification->toMail($user)->actionUrl;

                /** @var string $baseUrl */
                $baseUrl = config('gcstatus.front_base_url');

                $this->assertStringContainsString($token, $url);
                $this->assertStringContainsString("{$baseUrl}password/reset/{$token}/?email={$user->email}", $url);

                return true;
            }
        );
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

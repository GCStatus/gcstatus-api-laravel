<?php

namespace Tests\Feature\Http\EmailVerify;

use App\Models\User;
use App\Notifications\QueuedVerifyEmail;
use Illuminate\Support\Facades\Notification;
use Tests\Feature\Http\BaseIntegrationTesting;

class NotifyTest extends BaseIntegrationTesting
{
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

        $this->user = $this->actingAsDummyUser([
            'email_verified_at' => null,
        ]);
    }

    /**
     * Test if can't send email verification notification if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_send_email_verification_notification_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('verification.send'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't send email verification notification if already verified.
     *
     * @return void
     */
    public function test_if_cant_send_email_verification_notification_if_already_verified(): void
    {
        $this->user->markEmailAsVerified();

        $this->getJson(route('verification.send'))
            ->assertBadRequest()
            ->assertSee('You already verified your email, no one more action is required.');
    }

    /**
     * Test if can send email verification notification if authenticated.
     *
     * @return void
     */
    public function test_if_can_send_email_verification_notification_if_authenticated(): void
    {
        $this->getJson(route('verification.send'))->assertOk();
    }

    /**
     * Test if can dispatch notification event.
     *
     * @return void
     */
    public function test_if_can_dispatch_notification_event(): void
    {
        Notification::fake();

        $this->getJson(route('verification.send'))->assertOk();

        Notification::assertSentTo($this->user, QueuedVerifyEmail::class, fn () => true);
    }

    /**
     * Test if can respond with correct json structure.
     *
     * @return void
     */
    public function test_if_can_respond_with_correct_json_structure(): void
    {
        $this->getJson(route('verification.send'))->assertOk()->assertJsonStructure([
            'data' => [
                'message',
            ],
        ]);
    }

    /**
     * Test if can respond with correct json data.
     *
     * @return void
     */
    public function test_if_can_respond_with_correct_json_data(): void
    {
        $this->getJson(route('verification.send'))->assertOk()->assertJson([
            'data' => [
                'message' => 'The verification link will be sent to your email!',
            ],
        ]);
    }
}

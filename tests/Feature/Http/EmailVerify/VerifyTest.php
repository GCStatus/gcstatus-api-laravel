<?php

namespace Tests\Feature\Http\EmailVerify;

use App\Models\User;
use Illuminate\Support\Facades\URL;
use Tests\Feature\Http\BaseIntegrationTesting;

class VerifyTest extends BaseIntegrationTesting
{
    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The valid signed url.
     *
     * @var string
     */
    private string $url;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware('signed');
        $this->user = $this->actingAsDummyUser([
            'email_verified_at' => null,
        ]);
        $this->url = URL::signedRoute('verification.verify', [
            'id' => $this->user->id,
            'hash' => sha1($this->user->email),
        ]);
    }

    /**
     * Test if can't verify email if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_verify_email_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('verification.verify', [
            'id' => 1,
            'hash' => 'invalid',
        ]))->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't verify email if url is not valid signed.
     *
     * @return void
     */
    public function test_if_cant_verify_email_if_url_is_not_valid_signed(): void
    {
        $this->getJson(route('verification.verify', [
            'id' => $this->user->getKey(),
            'hash' => sha1($this->user->getEmailForVerification()),
        ]))->assertForbidden()
            ->assertSee('Invalid signature.');
    }

    /**
     * Test if can't verify email if already verified.
     *
     * @return void
     */
    public function test_if_cant_verify_email_if_already_verified(): void
    {
        $this->user->markEmailAsVerified();

        $this->getJson($this->url)
            ->assertBadRequest()
            ->assertSee('You already verified your email, no one more action is required.');
    }

    /**
     * Test if can mark user email as verified.
     *
     * @return void
     */
    public function test_if_can_mark_user_email_as_verified(): void
    {
        $this->assertFalse($this->user->hasVerifiedEmail());

        $this->getJson($this->url)->assertFound();

        $this->user->refresh();

        $this->assertTrue($this->user->hasVerifiedEmail());
    }

    /**
     * Test if can redirects user for correct path.
     *
     * @return void
     */
    public function test_if_can_redirects_user_for_correct_path(): void
    {
        /** @var string $path */
        $path = config('gcstatus.front_base_url');

        $this->getJson($this->url)->assertFound()->assertRedirect($path);
    }
}

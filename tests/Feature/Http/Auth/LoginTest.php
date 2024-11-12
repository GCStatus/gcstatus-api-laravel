<?php

namespace Tests\Feature\Http\Auth;

use App\Models\User;
use Tests\Traits\HasDummyUser;
use Illuminate\Support\Facades\Cookie;
use Tests\Feature\Http\baseIntegrationTesting;

class LoginTest extends baseIntegrationTesting
{
    use HasDummyUser;

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

        $this->user = $this->createDummyUser([
            'email' => 'valid@gmail.com',
        ]);
    }

    /**
     * Test if can't login with invalid identifier.
     *
     * @return void
     */
    public function test_if_cant_login_with_invalid_identifier(): void
    {
        $this->postJson(route('auth.login'), [
            'identifier' => 'i n v a l i d',
            'password' => 'admin1234',
        ])->assertUnprocessable()
            ->assertSee('The provided identifier is invalid. Please, use your email or nickname to proceed.');
    }

    /**
     * Can authenticate with nickname.
     *
     * @return void
     */
    public function test_if_can_authenticate_with_nickname(): void
    {
        $this->postJson(route('auth.login'), [
            'identifier' => $this->user->nickname,
            'password' => 'admin1234',
        ])->assertOk()->assertSee('User successfully authenticated.');
    }

    /**
     * Can authenticate with email.
     *
     * @return void
     */
    public function test_if_can_authenticate_with_email(): void
    {
        $this->postJson(route('auth.login'), [
            'identifier' => $this->user->email,
            'password' => 'admin1234',
        ])->assertOk()->assertSee('User successfully authenticated.');
    }

    /**
     * Test if can set the is auth cookie.
     *
     * @return void
     */
    public function test_if_can_set_the_is_auth_cookie(): void
    {
        /** @var string $tokenKey*/
        $tokenKey = config('auth.is_auth_key');

        $this->postJson(route('auth.login'), [
            'identifier' => $this->user->email,
            'password' => 'admin1234',
        ])->assertOk()->assertCookie($tokenKey)->assertCookieNotExpired($tokenKey);

        Cookie::hasQueued($tokenKey);
    }

    /**
     * Test if can set the token cookie.
     *
     * @return void
     */
    public function test_if_can_set_the_token_cookie(): void
    {
        /** @var string $tokenKey*/
        $tokenKey = config('auth.token_key');

        $this->postJson(route('auth.login'), [
            'identifier' => $this->user->email,
            'password' => 'admin1234',
        ])->assertOk()->assertCookie($tokenKey)->assertCookieNotExpired($tokenKey);

        Cookie::hasQueued($tokenKey);
    }
}

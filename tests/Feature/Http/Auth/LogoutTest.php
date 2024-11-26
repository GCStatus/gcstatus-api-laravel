<?php

namespace Tests\Feature\Http\Auth;

use Symfony\Component\HttpFoundation\Cookie;
use Tests\Feature\Http\BaseIntegrationTesting;

class LogoutTest extends BaseIntegrationTesting
{
    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->actingAsDummyUser();
    }

    /**
     * Test if can't perform a logout if user is not authenticated.
     *
     * @return void
     */
    public function test_if_cant_perform_a_logout_if_user_is_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->postJson(route('auth.logout'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can successfully make a post request to logout.
     *
     * @return void
     */
    public function test_if_can_successfully_make_a_post_request_to_logout(): void
    {
        $this->postJson(route('auth.logout'))->assertOk();
    }

    /**
     * Test if can successfully remove cookie authentication on logout.
     *
     * @return void
     */
    public function test_if_can_successfully_remove_cookie_authentication_on_logout(): void
    {
        $key = "fake_token_key";

        $authResponse = $this->getJson(route('auth.me'))->assertOk();

        $this->assertNotNull($authResponse->headers->getCookies());
        $this->assertTrue(collect($authResponse->headers->getCookies())->contains(function (Cookie $cookie) use ($key) {
            return $cookie->getName() === $key && $cookie->getExpiresTime() > time();
        }));

        $logoutResponse = $this->postJson(route('auth.logout'))->assertOk();

        $this->assertTrue(collect($logoutResponse->headers->getCookies())->contains(function (Cookie $cookie) use ($key) {
            return $cookie->getName() === $key && $cookie->getExpiresTime() <= time();
            ;
        }));

        $this->getJson(route('auth.me'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can get correct json structure on logout.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_structure_on_logout(): void
    {
        $this->postJson(route('auth.logout'))->assertOk()->assertJsonStructure([
            'data' => [
                'message',
            ],
        ]);
    }

    /**
     * Test if can get correct json data on logout.
     *
     * @return void
     */
    public function test_if_can_get_correct_json_data_on_logout(): void
    {
        $this->postJson(route('auth.logout'))->assertOk()->assertJson([
            'data' => [
                'message' => 'You have successfully logged out from platform!',
            ],
        ]);
    }
}

<?php

namespace Tests\Feature\Http;

use Symfony\Component\HttpFoundation\Cookie;

class BlockTest extends BaseIntegrationTesting
{
    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->actingAsDummyUser([
            'blocked' => true,
        ]);
    }

    /**
     * Test if can't access blockable routes if blocked.
     *
     * @return void
     */
    public function test_if_cant_access_blockable_routes_if_user_is_blocked(): void
    {
        $this->getJson(route('auth.me'))
            ->assertForbidden()
            ->assertSee('You are blocked from GCStatus. If you do not agree with this, please, contact the support.');
    }

    /**
     * Test if can access non blockable routes if blocked.
     *
     * @return void
     */
    public function test_if_can_access_non_blockable_routes_if_blocked(): void
    {
        $this->getJson(route('home'))->assertOk();
    }

    /**
     * Test if can logout user when user is blocked.
     *
     * @return void
     */
    public function test_if_can_logout_user_when_user_is_blocked(): void
    {
        /** @var string $key */
        $key = config('auth.token_key');

        $authResponse = $this->getJson(route('auth.me'))
            ->assertForbidden()
            ->assertSee('You are blocked from GCStatus. If you do not agree with this, please, contact the support.');

        $cookies = $authResponse->headers->getCookies();

        $this->assertNotEmpty($cookies);

        $this->assertTrue(collect($cookies)->contains(function (Cookie $cookie) use ($key): bool {
            return $cookie->getName() === $key && time() > $cookie->getExpiresTime();
        }));

        $this->getJson(route('auth.me'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }
}

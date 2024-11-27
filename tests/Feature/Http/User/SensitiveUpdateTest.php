<?php

namespace Tests\Feature\Http\User;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Tests\Feature\Http\BaseIntegrationTesting;

class SensitiveUpdateTest extends BaseIntegrationTesting
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

        $this->user = $this->actingAsDummyUser();
    }

    /**
     * Get valid payload.
     *
     * @return array<string, string>
     */
    private function getValidPayload(): array
    {
        return [
            'password' => 'admin1234',
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
        ];
    }

    /**
     * Test if can't update user sensitives if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_update_user_sensitives_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->putJson(route('users.sensitives.update'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't send update request without payload.
     *
     * @return void
     */
    public function test_if_cant_send_update_request_without_payload(): void
    {
        $this->putJson(route('users.sensitives.update'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->putJson(route('users.sensitives.update'), [
            'email' => null,
            'nickname' => null,
        ])->assertUnprocessable()
            ->assertInvalid(['password', 'email', 'nickname']);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->putJson(route('users.sensitives.update'), [
            'email' => null,
            'nickname' => null,
        ])->assertUnprocessable()
            ->assertInvalid(['password', 'email', 'nickname'])
            ->assertSee('The password field is required. (and 3 more errors)');
    }

    /**
     * Test if can't update user sensitive data without matching password.
     *
     * @return void
     */
    public function test_if_cant_update_user_sensitive_data_without_matching_password(): void
    {
        $data = [
            'password' => 'invalidpass',
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
        ];

        $this->putJson(route('users.sensitives.update'), $data)
            ->assertBadRequest()
            ->assertSee('Your password does not match.');
    }

    /**
     * Test if can send update request with valid payload.
     *
     * @return void
     */
    public function test_if_can_send_update_request_with_valid_payload(): void
    {
        $this->putJson(route('users.sensitives.update'), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can save the updates on database.
     *
     * @return void
     */
    public function test_if_can_save_the_updates_on_database(): void
    {
        $this->putJson(route('users.sensitives.update'), $data = $this->getValidPayload())->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'email' => $data['email'],
            'nickname' => $data['nickname'],
        ]);
    }

    /**
     * Test if can remove user from cache after update.
     *
     * @return void
     */
    public function test_if_can_remove_user_from_cache_after_update(): void
    {
        $identifier = $this->user->id;

        $key = "auth.user.$identifier";

        $this->getJson(route('auth.me'))->assertOk();

        $this->assertTrue(Cache::has($key));

        $this->putJson(route('users.sensitives.update'), $this->getValidPayload())->assertOk();

        $this->assertFalse(Cache::has($key));
    }

    /**
     * Test if can respond with correct json structure.
     *
     * @return void
     */
    public function test_if_can_respond_with_correct_json_structure(): void
    {
        $this->putJson(route('users.sensitives.update'), $this->getValidPayload())->assertOk()->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'level',
                'nickname',
                'birthdate',
                'experience',
                'created_at',
                'updated_at',
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
        $this->putJson(route('users.sensitives.update'), $data = $this->getValidPayload())->assertOk()->assertJson([
            'data' => [
                'id' => $this->user->id,
                'email' => $data['email'],
                'nickname' => $data['nickname'],
            ],
        ]);
    }
}

<?php

namespace Tests\Feature\Http\User;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\Feature\Http\BaseIntegrationTesting;

class BasicUpdateTest extends BaseIntegrationTesting
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
            'name' => fake()->name(),
            'birthdate' => Carbon::today()->subYears(
                fake()->numberBetween(15, 30),
            )->toDateString(),
        ];
    }

    /**
     * Test if can't update user basics if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_update_user_basics_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->putJson(route('users.basics.update'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can send update request without payload (not required).
     *
     * @return void
     */
    public function test_if_can_send_update_request_without_payload(): void
    {
        $this->putJson(route('users.basics.update'))->assertOk();
    }

    /**
     * Test if can send update request with valid payload.
     *
     * @return void
     */
    public function test_if_can_send_update_request_with_valid_payload(): void
    {
        $this->putJson(route('users.basics.update'), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can save the updates on database.
     *
     * @return void
     */
    public function test_if_can_save_the_updates_on_database(): void
    {
        $this->putJson(route('users.basics.update'), $data = $this->getValidPayload())->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'name' => $data['name'],
            'birthdate' => $data['birthdate'],
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

        $this->putJson(route('users.basics.update'), $this->getValidPayload())->assertOk();

        $this->assertFalse(Cache::has($key));
    }

    /**
     * Test if can respond with correct json structure.
     *
     * @return void
     */
    public function test_if_can_respond_with_correct_json_structure(): void
    {
        $this->putJson(route('users.basics.update'), $this->getValidPayload())->assertOk()->assertJsonStructure([
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
        $this->putJson(route('users.basics.update'), $data = $this->getValidPayload())->assertOk()->assertJson([
            'data' => [
                'id' => $this->user->id,
                'name' => $data['name'],
                'birthdate' => $data['birthdate'],
            ],
        ]);
    }
}

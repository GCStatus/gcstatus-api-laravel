<?php

namespace Tests\Feature\Http\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Tests\Feature\Http\BaseIntegrationTesting;

class SocialUpdateTest extends BaseIntegrationTesting
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
     * Get valid dummy data.
     *
     * @return array<string, mixed>
     */
    public function getValidPayload(): array
    {
        $url = 'https://google.com';

        return [
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'github' => $url,
            'twitch' => $url,
            'twitter' => $url,
            'youtube' => $url,
            'facebook' => $url,
            'instagram' => $url,
        ];
    }

    /**
     * Test if can't update the socials if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_update_socials_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->putJson(route('profiles.socials.update'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can't update the socials without payload.
     *
     * @return void
     */
    public function test_if_cant_update_the_socials_without_payload(): void
    {
        $this->putJson(route('profiles.socials.update'))->assertUnprocessable();
    }

    /**
     * Test if can throw correct invalid json keys.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_keys(): void
    {
        $this->putJson(route('profiles.socials.update'))
            ->assertUnprocessable()
            ->assertInvalid(['share']);
    }

    /**
     * Test if can throw correct invalid json messages.
     *
     * @return void
     */
    public function test_if_can_throw_correct_invalid_json_messages(): void
    {
        $this->putJson(route('profiles.socials.update'))
            ->assertUnprocessable()
            ->assertInvalid(['share'])
            ->assertSee('The share field is required.');
    }

    /**
     * Test if can't update the socials with invalid url.
     *
     * @return void
     */
    public function test_if_cant_update_the_socials_with_invalid_urls(): void
    {
        /** @var array<string, mixed> $data */
        $data = [
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'github' => 'invalid.com',
            'twitch' => 'invalid.com',
            'twitter' => 'invalid.com',
            'youtube' => 'invalid.com',
            'facebook' => 'invalid.com',
            'instagram' => 'invalid.com',
        ];

        $this->putJson(route('profiles.socials.update'), $data)
            ->assertUnprocessable()
            ->assertInvalid(['twitter', 'instagram', 'facebook', 'youtube', 'twitch', 'github'])
            ->assertSee('The instagram field must be a valid URL. (and 5 more errors)');
    }

    /**
     * Test if can update the socials with valid payload.
     *
     * @return void
     */
    public function test_if_can_update_the_socials_with_valid_payload(): void
    {
        $this->putJson(route('profiles.socials.update'), $this->getValidPayload())->assertOk();
    }

    /**
     * Test if can save the socials on database correctly.
     *
     * @return void
     */
    public function test_if_can_save_the_socials_on_database_correctly(): void
    {
        $this->putJson(route('profiles.socials.update'), $data = $this->getValidPayload())->assertOk();

        $this->assertDatabaseHas('profiles', [
            'user_id' => $this->user->id,
            'share' => $data['share'],
            'phone' => $data['phone'],
            'github' => $data['github'],
            'twitch' => $data['twitch'],
            'twitter' => $data['twitter'],
            'youtube' => $data['youtube'],
            'facebook' => $data['facebook'],
            'instagram' => $data['instagram'],
        ]);
    }

    /**
     * Test if can remove the user from cache on profile update.
     *
     * @return void
     */
    public function test_if_can_remove_the_user_from_cache_on_profile_update(): void
    {
        $identifier = $this->user->id;

        $key = "auth.user.$identifier";

        $this->getJson(route('auth.me'))->assertOk();

        $this->assertTrue(Cache::has($key));

        $this->putJson(route('profiles.socials.update'), $this->getValidPayload())->assertOk();

        $this->assertFalse(Cache::has($key));
    }

    /**
     * Test if can respond with valid json structure.
     *
     * @return void
     */
    public function test_if_can_respond_with_valid_json_structure(): void
    {
        $this->putJson(route('profiles.socials.update'), $this->getValidPayload())->assertOk()->assertJsonStructure([
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
                'wallet' => [
                    'id',
                    'amount',
                ],
                'profile' => [
                    'id',
                    'photo',
                    'share',
                    'phone',
                    'twitch',
                    'github',
                    'twitter',
                    'youtube',
                    'facebook',
                    'instagram',
                ],
            ],
        ]);
    }

    /**
     * Test if can respond with valid json data.
     *
     * @return void
     */
    public function test_if_can_respond_with_valid_json_data(): void
    {
        /** @var \App\Models\Profile $profile */
        $profile = $this->user->profile;

        $this->putJson(route('profiles.socials.update'), $data = $this->getValidPayload())->assertOk()->assertJson([
            'data' => [
                'profile' => [
                    'id' => $profile->id,
                    'photo' => $profile->photo,
                    'share' => $data['share'],
                    'phone' => $data['phone'],
                    'github' => $data['github'],
                    'twitch' => $data['twitch'],
                    'twitter' => $data['twitter'],
                    'youtube' => $data['youtube'],
                    'facebook' => $data['facebook'],
                    'instagram' => $data['instagram'],
                ],
            ],
        ]);
    }
}

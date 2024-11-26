<?php

namespace Tests\Feature\Http\User;

use App\Models\User;
use Tests\Feature\Http\BaseIntegrationTesting;

class UserMeTest extends BaseIntegrationTesting
{
    /**
     * The dummy authenticated user.
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
     * Test if can't get me details if not authenticated.
     *
     * @return void
     */
    public function test_if_cant_get_me_details_if_not_authenticated(): void
    {
        $this->authService->clearAuthenticationCookies();

        $this->getJson(route('auth.me'))
            ->assertUnauthorized()
            ->assertSee('We could not authenticate your user. Please, try to login again.');
    }

    /**
     * Test if can send a get request and returns ok.
     *
     * @return void
     */
    public function test_if_can_send_a_get_request_and_returns_ok(): void
    {
        $this->getJson(route('auth.me'))->assertOk();
    }

    /**
     * Test if can get correctly me json attributes count.
     *
     * @return void
     */
    public function test_if_can_get_correctly_me_json_attributes_count(): void
    {
        $this->getJson(route('auth.me'))->assertOk()->assertJsonCount(11, 'data');
    }

    /**
     * Test if can get correctly me json structure.
     *
     * @return void
     */
    public function test_if_can_get_me_correctly_json_structure(): void
    {
        $this->getJson(route('auth.me'))->assertOk()->assertJsonStructure([
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
            ]
        ]);
    }

    /**
     * Test if can get correctly me json data.
     *
     * @return void
     */
    public function test_if_can_get_correctly_me_json_data(): void
    {
        /** @var \App\Models\Wallet $wallet */
        $wallet = $this->user->wallet;

        /** @var \App\Models\Profile $profile */
        $profile = $this->user->profile;

        /** @var \App\Models\Level $level */
        $level = $this->user->level;

        $this->getJson(route('auth.me'))->assertOk()->assertJson([
            'data' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'level' => $level->level,
                'nickname' => $this->user->nickname,
                'birthdate' => $this->user->birthdate,
                'experience' => $this->user->experience,
                'created_at' => $this->user->created_at?->toISOString(),
                'updated_at' => $this->user->updated_at?->toISOString(),
                'wallet' => [
                    'id' => $wallet->id,
                    'amount' => $wallet->amount,
                ],
                'profile' => [
                    'id' => $profile->id,
                    'photo' => $profile->photo,
                    'share' => $profile->share,
                    'phone' => $profile->phone,
                    'twitch' => $profile->twitch,
                    'github' => $profile->github,
                    'twitter' => $profile->twitter,
                    'youtube' => $profile->youtube,
                    'facebook' => $profile->facebook,
                    'instagram' => $profile->instagram,
                ],
            ]
        ]);
    }
}

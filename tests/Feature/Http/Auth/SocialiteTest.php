<?php

namespace Tests\Feature\Http\Auth;

use Mockery;
use Tests\TestCase;
use Tests\Traits\HasDummyUser;
use Database\Seeders\LevelSeeder;
use Illuminate\Support\Facades\Cookie;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as TwoUser;
use Laravel\Socialite\Two\AbstractProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SocialiteTest extends TestCase
{
    use HasDummyUser;
    use RefreshDatabase;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([LevelSeeder::class]);
    }

    /**
     * Test if can redirect user to provider.
     *
     * @return void
     */
    public function test_redirect_to_provider(): void
    {
        $provider = 'google';

        $response = $this->getJson(route('auth.socialite.redirect', [
            'provider' => $provider,
        ]));

        /** @var string $redirectUrl */
        $redirectUrl = $response->headers->get('Location');
        $this->assertStringStartsWith('https://accounts.google.com/o/oauth2/auth', $redirectUrl);
    }

    /**
     * Test if can redirect correctly user from callback.
     *
     * @return void
     */
    public function test_if_can_redirect_correctly_user_from_callback(): void
    {
        $provider = 'google';
        $socialiteUser = Mockery::mock(TwoUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn(123);
        $socialiteUser->shouldReceive('getName')->andReturn(fake()->name());
        $socialiteUser->shouldReceive('getEmail')->andReturn(fake()->safeEmail());
        $socialiteUser->shouldReceive('getAvatar')->andReturn(fake()->imageUrl());
        $socialiteUser->shouldReceive('getNickname')->andReturn(fake()->userName());

        $providerMock = Mockery::mock(AbstractProvider::class);
        $providerMock->shouldReceive('stateless')
            ->once()
            ->andReturnSelf();
        $providerMock->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with($provider)
            ->once()
            ->andReturn($providerMock);

        $response = $this->getJson(route('auth.socialite.callback', [
            'provider' => $provider,
        ]));

        /** @var string $base */
        $base = config('gcstatus.front_base_url');
        $path = $base . 'register/complete';
        $response->assertRedirect($path);
    }

    /**
     * Test the callback handling and save on database.
     *
     * @return void
     */
    public function test_callback_handling_and_save_on_database(): void
    {
        $name = fake()->name();
        $email = fake()->safeEmail();
        $avatar = fake()->imageUrl();
        $nickname = fake()->userName();

        $this->assertDatabaseEmpty('users');
        $this->assertDatabaseEmpty('wallets');
        $this->assertDatabaseEmpty('profiles');
        $this->assertDatabaseEmpty('social_scopes');
        $this->assertDatabaseEmpty('social_accounts');

        $provider = 'google';
        $socialiteUser = Mockery::mock(TwoUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn(123);
        $socialiteUser->shouldReceive('getName')->andReturn($name);
        $socialiteUser->shouldReceive('getEmail')->andReturn($email);
        $socialiteUser->shouldReceive('getAvatar')->andReturn($avatar);
        $socialiteUser->shouldReceive('getNickname')->andReturn($nickname);

        /** @var \Laravel\Socialite\Two\User $socialiteUser */
        $socialiteUser->approvedScopes = ['scope1', 'scope2'];

        $providerMock = Mockery::mock(AbstractProvider::class);
        $providerMock->shouldReceive('stateless')
            ->once()
            ->andReturnSelf();
        $providerMock->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with($provider)
            ->once()
            ->andReturn($providerMock);

        $this->getJson(route('auth.socialite.callback', [
            'provider' => $provider,
        ]));

        $this->assertDatabaseCount('users', 1)->assertDatabaseHas('users', [
            'id' => 1,
            'name' => $name,
            'nickname' => $nickname,
            'email' => $email,
            'password' => null,
            'level_id' => 1,
        ]);
        $this->assertDatabaseCount('wallets', 1)->assertDatabaseHas('wallets', [
            'user_id' => 1,
            'amount' => 0,
        ]);
        $this->assertDatabaseCount('profiles', 1)->assertDatabaseHas('profiles', [
            'user_id' => 1,
            'share' => false,
            'photo' => $avatar,
        ]);
        $this->assertDatabaseCount('social_accounts', 1)->assertDatabaseHas('social_accounts', [
            'user_id' => 1,
            'provider' => $provider,
            'provider_id' => $socialiteUser->getId(),
        ]);
        $this->assertDatabaseCount('social_scopes', 2);

        foreach ($socialiteUser->approvedScopes as $scope) {
            $this->assertDatabaseHas('social_scopes', [
                'scope' => $scope,
                'social_account_id' => 1,
            ]);
        }
    }

    /**
     * Test if can't create another user if already exists for given email.
     *
     * @return void
     */
    public function test_if_cant_create_another_user_if_already_exists_for_given_email(): void
    {
        $this->assertDatabaseEmpty('users');
        $this->assertDatabaseEmpty('wallets');
        $this->assertDatabaseEmpty('profiles');
        $this->assertDatabaseEmpty('social_scopes');
        $this->assertDatabaseEmpty('social_accounts');

        $provider = 'google';
        $socialiteUser = Mockery::mock(TwoUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn(123);
        $socialiteUser->shouldReceive('getName')->andReturn(fake()->name());
        $socialiteUser->shouldReceive('getEmail')->andReturn(fake()->safeEmail());
        $socialiteUser->shouldReceive('getAvatar')->andReturn(fake()->imageUrl());
        $socialiteUser->shouldReceive('getNickname')->andReturn(fake()->userName());

        /** @var \Laravel\Socialite\Two\User $socialiteUser */
        $socialiteUser->approvedScopes = ['scope1', 'scope2'];

        $providerMock = Mockery::mock(AbstractProvider::class);
        $providerMock->shouldReceive('stateless')
            ->twice()
            ->andReturnSelf();
        $providerMock->shouldReceive('user')
            ->twice()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with($provider)
            ->twice()
            ->andReturn($providerMock);

        $this->getJson(route('auth.socialite.callback', [
            'provider' => $provider,
        ]));

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('wallets', 1);
        $this->assertDatabaseCount('profiles', 1);
        $this->assertDatabaseCount('social_scopes', 2);
        $this->assertDatabaseCount('social_accounts', 1);

        $this->getJson(route('auth.socialite.callback', [
            'provider' => $provider,
        ]));

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('wallets', 1);
        $this->assertDatabaseCount('profiles', 1);
        $this->assertDatabaseCount('social_scopes', 2);
        $this->assertDatabaseCount('social_accounts', 1);
    }

    /**
     * Test if can't replace user image from provider if user already have a picture.
     *
     * @return void
     */
    public function test_if_cant_replace_user_image_from_provider_if_user_already_have_a_picture(): void
    {
        $name = fake()->name();
        $email = fake()->safeEmail();
        $avatar = fake()->imageUrl();
        $nickname = fake()->userName();

        $user = $this->createDummyUser([
            'name' => $name,
            'nickname' => $nickname,
            'email' => $email,
        ]);

        $user->profile()->update([
            'share' => false,
            'photo' => $avatar,
        ]);

        $provider = 'google';
        $socialiteUser = Mockery::mock(TwoUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn(123);
        $socialiteUser->shouldReceive('getName')->andReturn($name);
        $socialiteUser->shouldReceive('getEmail')->andReturn($email);
        $socialiteUser->shouldReceive('getNickname')->andReturn($nickname);
        $socialiteUser->shouldReceive('getAvatar')->andReturn($another = fake()->imageUrl());

        /** @var \Laravel\Socialite\Two\User $socialiteUser */
        $socialiteUser->approvedScopes = ['scope1', 'scope2'];

        $providerMock = Mockery::mock(AbstractProvider::class);
        $providerMock->shouldReceive('stateless')
            ->once()
            ->andReturnSelf();
        $providerMock->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with($provider)
            ->once()
            ->andReturn($providerMock);

        $this->getJson(route('auth.socialite.callback', [
            'provider' => $provider,
        ]));

        $this->assertDatabaseCount('profiles', 1)->assertDatabaseHas('profiles', [
            'user_id' => 1,
            'share' => false,
            'photo' => $avatar,
        ])->assertDatabaseMissing('profiles', [
            'photo' => $another,
        ]);
    }

    /**
     * Test if can set the authenticated cookies for the callback handling.
     *
     * @return void
     */
    public function test_if_can_set_the_authenticated_cookies_for_the_callback_handling(): void
    {
        $provider = 'google';
        $socialiteUser = Mockery::mock(TwoUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn(123);
        $socialiteUser->shouldReceive('getName')->andReturn(fake()->name());
        $socialiteUser->shouldReceive('getEmail')->andReturn(fake()->safeEmail());
        $socialiteUser->shouldReceive('getAvatar')->andReturn(fake()->imageUrl());
        $socialiteUser->shouldReceive('getNickname')->andReturn(fake()->userName());

        /** @var \Laravel\Socialite\Two\User $socialiteUser */
        $socialiteUser->approvedScopes = ['scope1', 'scope2'];

        $providerMock = Mockery::mock(AbstractProvider::class);
        $providerMock->shouldReceive('stateless')
            ->once()
            ->andReturnSelf();
        $providerMock->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with($provider)
            ->once()
            ->andReturn($providerMock);

        $this->getJson(route('auth.socialite.callback', [
            'provider' => $provider,
        ]));

        /** @var string $tokenKey*/
        $tokenKey = config('auth.token_key');
        Cookie::hasQueued($tokenKey);

        /** @var string $tokenKey */
        $tokenKey = config('auth.is_auth_key');
        Cookie::hasQueued($tokenKey);
    }
}

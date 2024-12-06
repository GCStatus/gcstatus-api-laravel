<?php

namespace Tests\Unit\Services;

use Mockery;
use DateTime;
use Tests\TestCase;
use Mockery\MockInterface;
use Illuminate\Support\Str;
use App\Services\SocialiteService;
use App\Models\{User, SocialAccount};
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Two\User as SocialiteUser;
use App\Contracts\Repositories\SocialiteRepositoryInterface;
use App\Contracts\Services\{
    AuthServiceInterface,
    CacheServiceInterface,
    JWTServiceInterface,
    ProfileServiceInterface,
    SocialAccountServiceInterface,
    SocialiteServiceInterface,
    SocialScopeServiceInterface,
};

class SocialiteServiceTest extends TestCase
{
    /**
     * The socialite service.
     *
     * @var \App\Contracts\Services\SocialiteServiceInterface
     */
    private SocialiteServiceInterface $socialiteService;

    /**
     * The mock socialite repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $mockSocialiteRepository;

    /**
     * The mock cache service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $mockCacheService;

    /**
     * The mock social account service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $mockSocialAccountService;

    /**
     * The mock social scope service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $mockSocialScopeService;

    /**
     * The mock profile service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $mockProfileService;

    /**
     * The mock auth service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $mockAuthService;

    /**
     * The mock jwt service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $mockJwtService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->socialiteService = app(SocialiteServiceInterface::class);

        $this->mockSocialiteRepository = Mockery::mock(SocialiteRepositoryInterface::class);
        $this->mockCacheService = Mockery::mock(CacheServiceInterface::class);
        $this->mockSocialAccountService = Mockery::mock(SocialAccountServiceInterface::class);
        $this->mockSocialScopeService = Mockery::mock(SocialScopeServiceInterface::class);
        $this->mockProfileService = Mockery::mock(ProfileServiceInterface::class);
        $this->mockAuthService = Mockery::mock(AuthServiceInterface::class);
        $this->mockJwtService = Mockery::mock(JWTServiceInterface::class);

        /** @var \App\Contracts\Repositories\SocialiteRepositoryInterface $socialiteRepository */
        $socialiteRepository = $this->mockSocialiteRepository;
        /** @var \App\Contracts\Services\CacheServiceInterface $cacheService */
        $cacheService = $this->mockCacheService;
        /** @var \App\Contracts\Services\SocialAccountServiceInterface $socialAccountService */
        $socialAccountService = $this->mockSocialAccountService;
        /** @var \App\Contracts\Services\SocialScopeServiceInterface $socialScopeService */
        $socialScopeService = $this->mockSocialScopeService;
        /** @var \App\Contracts\Services\ProfileServiceInterface $profileService */
        $profileService = $this->mockProfileService;
        /** @var \App\Contracts\Services\AuthServiceInterface $authService */
        $authService = $this->mockAuthService;
        /** @var \App\Contracts\Services\JWTServiceInterface $jwtService */
        $jwtService = $this->mockJwtService;

        $this->socialiteService = new SocialiteService(
            $socialiteRepository,
            $cacheService,
            $socialAccountService,
            $socialScopeService,
            $profileService,
            $authService,
            $jwtService,
        );
    }

    /**
     * Test if can redirects the user to provider authentication.
     *
     * @return void
     */
    public function test_if_can_redirects_the_user_to_provider_authentication(): void
    {
        $provider = 'github';
        $mockRedirectResponse = Mockery::mock(RedirectResponse::class);

        $this->mockCacheService
            ->shouldReceive('put')
            ->once()
            ->withArgs(function (string $key, bool $value, DateTime $expiration) use (&$state) {
                $state = str_replace('oauth_state_', '', $key);
                return !!$value;
            });

        $this->mockSocialiteRepository
            ->shouldReceive('redirect')
            ->once()
            ->with($provider, Mockery::on(function (string $actualState) use (&$state) {
                return $actualState === $state;
            }))->andReturn($mockRedirectResponse);

        $result = $this->socialiteService->redirect($provider);

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals($mockRedirectResponse, $result);
    }

    /**
     * Test if can get the callback user.
     *
     * @return void
     */
    public function test_if_can_get_correct_callback_user(): void
    {
        $provider = 'github';
        $mockSocialiteUser = Mockery::mock(SocialiteUser::class);

        $this->mockSocialiteRepository
            ->shouldReceive('getCallbackUser')
            ->once()
            ->with($provider)
            ->andReturn($mockSocialiteUser);

        $result = $this->socialiteService->getCallbackUser($provider);

        $this->assertInstanceOf(SocialiteUser::class, $result);
    }

    /**
     * Test if can generate state correctly.
     *
     * @return void
     */
    public function test_if_can_generate_state(): void
    {
        $result = $this->socialiteService->generateState();

        $this->assertTrue(Str::length($result) === 40);
    }

    /**
     * Test if can set state on cache.
     *
     * @return void
     */
    public function test_if_can_set_state_on_cache(): void
    {
        $state = $this->socialiteService->generateState();

        $key = 'oauth_state_' . $state;

        $this->mockCacheService
            ->shouldReceive('put')
            ->once()
            ->with($key, true, Mockery::on(function ($expiration) {
                return $expiration instanceof DateTime;
            }))->andReturnTrue();

        $this->socialiteService->setState($state);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can pull state from cache.
     *
     * @return void
     */
    public function test_if_can_pull_state_from_cache(): void
    {
        $key = 'oauth_state_' . Str::random(40);

        $this->mockCacheService
            ->shouldReceive('pull')
            ->once()
            ->with($key)
            ->andReturnTrue();

        $this->socialiteService->pullState($key);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can format social user.
     *
     * @return void
     */
    public function test_if_can_format_social_user(): void
    {
        $id = 1;
        $name = fake()->name();
        $email = fake()->safeEmail();
        $nickname = fake()->userName();

        $mockSocialiteUser = Mockery::mock(SocialiteUser::class);
        $mockSocialiteUser->shouldReceive('getId')->once()->andReturn($id);
        $mockSocialiteUser->shouldReceive('getName')->once()->andReturn($name);
        $mockSocialiteUser->shouldReceive('getEmail')->once()->andReturn($email);
        $mockSocialiteUser->shouldReceive('getNickname')->once()->andReturn($nickname);

        /** @var \Laravel\Socialite\Two\User $mockSocialiteUser */
        $result = $this->socialiteService->formatSocialUser($mockSocialiteUser);

        $this->assertEqualsCanonicalizing([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'nickname' => $nickname,
        ], $result);
    }

    /**
     * Test if can correctly associate socials.
     *
     * @return void
     */
    public function test_if_can_correctly_associate_socials(): void
    {
        $provider = 'google';
        $scopes = ['scope1', 'scope2'];

        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockSocialiteUser = Mockery::mock(SocialiteUser::class);
        $mockSocialAccount = Mockery::mock(SocialAccount::class)->makePartial();

        $mockSocialiteUser->shouldReceive('getId')->twice()->andReturn(123);
        $mockUser->shouldReceive('getAttribute')->once()->with('id')->andReturn(1);

        /** @var \App\Models\User $mockUser */
        /** @var \App\Models\SocialAccount $mockSocialAccount */
        /** @var \Laravel\Socialite\Two\User $mockSocialiteUser */

        $mockSocialAccount->id = 456;
        $mockSocialiteUser->approvedScopes = $scopes;
        $mockSocialAccount->wasRecentlyCreated = true;

        $this->mockSocialAccountService
            ->shouldReceive('firstOrCreate')
            ->once()
            ->with([
                'user_id' => 1,
                'provider' => $provider,
            ], [
                'provider_id' => $mockSocialiteUser->getId(),
            ])->andReturn($mockSocialAccount);

        $this->mockSocialScopeService
            ->shouldReceive('firstOrCreate')
            ->twice()
            ->withArgs(function (array $attributes) {
                return isset($attributes['scope'], $attributes['social_account_id']) &&
                    $attributes['social_account_id'] === 456 &&
                    in_array($attributes['scope'], ['scope1', 'scope2'], true);
            });

        $this->socialiteService->associateSocials($provider, $mockUser, $mockSocialiteUser);
        $this->assertEquals(4, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can update user profile avatar.
     *
     * @return void
     */
    public function test_if_can_update_user_profile_avatar(): void
    {
        $mockUser = Mockery::mock(User::class)->makePartial();
        $mockSocialiteUser = Mockery::mock(SocialiteUser::class);
        $mockSocialiteUser->shouldReceive('getAvatar')->twice()->andReturn(fake()->imageUrl());

        /** @var \App\Models\User $mockUser */
        /** @var \Laravel\Socialite\Two\User $mockSocialiteUser */

        $this->mockProfileService
            ->shouldReceive('updateForUser')
            ->once()
            ->with($mockUser, [
                'photo' => $mockSocialiteUser->getAvatar(),
            ]);

        $this->socialiteService->updateAvatar($mockUser, $mockSocialiteUser);
        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can get the correct redirect path.
     *
     * @return void
     */
    public function test_if_can_get_the_correct_redirect_path(): void
    {
        // Recently created
        $mockUser = Mockery::mock(User::class)->makePartial();

        /** @var string $base */
        $base = config('gcstatus.front_base_url');

        /** @var non-falsy-string $path */
        $path = $base . 'register/complete';

        /** @var \App\Models\User $mockUser */
        $mockUser->wasRecentlyCreated = true;

        $result = $this->socialiteService->getRedirectablePath($mockUser);

        $this->assertEquals($path, $result);

        // Non recently created
        /** @var string $path */
        $path2 = $base;
        $mockUser->wasRecentlyCreated = false;

        $result2 = $this->socialiteService->getRedirectablePath($mockUser);

        $this->assertEquals($path2, $result2);
    }

    /**
     * Test if can authenticate the user from socialite.
     *
     * @return void
     */
    public function test_if_can_authenticate_the_user_from_socialite(): void
    {
        $token = 'token1234';
        $mockUser = Mockery::mock(User::class)->makePartial();

        $this->mockJwtService
            ->shouldReceive('tokenize')
            ->once()
            ->with($mockUser)
            ->andReturn($token);

        $this->mockAuthService
            ->shouldReceive('setAuthenticationCookies')
            ->once()
            ->with($token);

        /** @var \App\Models\User $mockUser */
        $this->socialiteService->authenticate($mockUser);
        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Cleanup Mockery after each test.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}

<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use Mockery\MockInterface;
use App\Services\AuthService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use App\Exceptions\Auth\InvalidIdentifierException;
use App\Contracts\Repositories\AuthRepositoryInterface;
use App\Contracts\Services\{
    CacheServiceInterface,
    CryptServiceInterface,
    CookieServiceInterface,
    Validation\IdentifierValidatorInterface,
};

class AuthServiceTest extends TestCase
{
    /**
     * The mock repository.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $mockRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mockRepository = Mockery::mock(AuthRepositoryInterface::class);
    }

    /**
     * Test if can validate an email.
     *
     * @return void
     */
    public function test_if_can_validate_an_email(): void
    {
        $token = 'token123';
        $mail = 'example@gmail.com';

        $this->app->bind(IdentifierValidatorInterface::class, function (Application $app, array $params) use ($mail) {
            $mockValidator = Mockery::mock(IdentifierValidatorInterface::class);
            $identifierType = $params['type'] ?? null;

            if ($identifierType === 'email') {
                $mockValidator->shouldReceive('validate')
                    ->once()
                    ->with($mail)
                    ->andReturnTrue();
            } elseif ($identifierType === 'nickname') {
                $mockValidator->shouldReceive('validate')->never();
            }

            return $mockValidator;
        });

        $credentials = [
            'identifier' => $mail,
            'password' => 'password123'
        ];

        $this->mockRepository->shouldReceive('authByEmail')
            ->once()
            ->with($credentials)
            ->andReturn($token);

        /** @var \App\Contracts\Repositories\AuthRepositoryInterface $mockRepository */
        $mockRepository = $this->mockRepository;
        $authService = new AuthService(
            $mockRepository,
            app(CryptServiceInterface::class),
            app(CookieServiceInterface::class),
            app(CacheServiceInterface::class),
        );

        $this->assertEquals($token, $authService->auth($credentials));
    }

    /**
     * Test if can validate an username.
     *
     * @return void
     */
    public function test_if_can_validate_an_username(): void
    {
        $token = 'token123';
        $nickname = fake()->userName();

        $this->app->bind(IdentifierValidatorInterface::class, function (Application $app, array $params) use ($nickname) {
            $mockValidator = Mockery::mock(IdentifierValidatorInterface::class);
            $identifierType = $params['type'] ?? null;

            if ($identifierType === 'email') {
                $mockValidator->shouldReceive('validate')
                    ->once()
                    ->with($nickname)
                    ->andReturnFalse();
            } elseif ($identifierType === 'nickname') {
                $mockValidator->shouldReceive('validate')
                    ->once()
                    ->with($nickname)
                    ->andReturnTrue();
            }

            return $mockValidator;
        });

        $credentials = [
            'identifier' => $nickname,
            'password' => 'password123'
        ];

        $this->mockRepository->shouldReceive('authByNickname')
            ->once()
            ->with($credentials)
            ->andReturn($token);

        /** @var \App\Contracts\Repositories\AuthRepositoryInterface $mockRepository */
        $mockRepository = $this->mockRepository;
        $authService = new AuthService(
            $mockRepository,
            app(CryptServiceInterface::class),
            app(CookieServiceInterface::class),
            app(CacheServiceInterface::class),
        );

        $this->assertEquals($token, $authService->auth($credentials));
    }

    /**
     * Test if can fail if validation not pass.
     *
     * @return void
     */
    public function test_if_can_fail_if_validation_not_pass(): void
    {
        $badIdentifier = 'fake 123';

        $this->app->bind(IdentifierValidatorInterface::class, function (Application $app, array $params) use ($badIdentifier) {
            $mockValidator = Mockery::mock(IdentifierValidatorInterface::class);

            $mockValidator->shouldReceive('validate')
                ->once()
                ->with($badIdentifier)
                ->andReturnFalse();

            return $mockValidator;
        });

        $credentials = [
            'identifier' => $badIdentifier,
            'password' => 'password123'
        ];

        $this->expectException(InvalidIdentifierException::class);
        $this->expectExceptionMessage('The provided identifier is invalid. Please, use your email or nickname to proceed.');

        /** @var \App\Contracts\Repositories\AuthRepositoryInterface $mockRepository */
        $mockRepository = $this->mockRepository;
        $authService = new AuthService(
            $mockRepository,
            app(CryptServiceInterface::class),
            app(CookieServiceInterface::class),
            app(CacheServiceInterface::class),
        );

        $authService->auth($credentials);
    }

    /**
     * Test if can set authentication cookies.
     *
     * @return void
     */
    public function test_if_can_set_authentication_cookies(): void
    {
        $token = fake()->word();
        $encryptedToken = 'encrypted_' . $token;

        $mockCryptService = Mockery::mock(CryptServiceInterface::class);
        $mockCryptService->shouldReceive('encrypt')
            ->once()
            ->with($token)
            ->andReturn($encryptedToken);

        $tokenKey = config('auth.token_key');
        $isAuthKey = config('auth.is_auth_key');
        $ttl = config('jwt.ttl');
        $path = config('session.path');
        $domain = config('session.domain');
        $secure = config('session.secure');
        $httpOnly = config('session.http_only');

        $mockCookieService = Mockery::mock(CookieServiceInterface::class);
        $mockCookieService
            ->shouldReceive('queue')
            ->once()
            ->with($tokenKey, $encryptedToken, $ttl, $path, $domain, $secure, $httpOnly);

        $mockCookieService
            ->shouldReceive('queue')
            ->once()
            ->with($isAuthKey, '1', $ttl, $path, $domain, false, false);

        /** @var \App\Contracts\Repositories\AuthRepositoryInterface $mockRepository */
        $mockRepository = $this->mockRepository;

        /** @var \App\Contracts\Services\CookieServiceInterface $mockCookieService */
        $mockCookieService = $mockCookieService;

        /** @var \App\Contracts\Services\CryptServiceInterface $mockCryptService */
        $mockCryptService = $mockCryptService;

        $authService = new AuthService(
            $mockRepository,
            $mockCryptService,
            $mockCookieService,
            app(CacheServiceInterface::class),
        );

        $authService->setAuthenticationCookies($token);

        $this->assertEquals(3, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can get the authenticated user id.
     *
     * @return void
     */
    public function test_if_can_get_the_authenticated_user_id(): void
    {
        $this->mockRepository
            ->shouldReceive('getAuthId')
            ->once()
            ->andReturn(1);

        /** @var \App\Contracts\Repositories\AuthRepositoryInterface $mockRepository */
        $mockRepository = $this->mockRepository;
        $authService = new AuthService(
            $mockRepository,
            app(CryptServiceInterface::class),
            app(CookieServiceInterface::class),
            app(CacheServiceInterface::class),
        );

        $result = $authService->getAuthId();

        $this->assertSame(1, $result);
    }

    /**
     * Test if can clear authentication cookies.
     *
     * @return void
     */
    public function test_if_can_clear_authentication_cookies(): void
    {
        $tokenKey = config('auth.token_key');
        $isAuthKey = config('auth.is_auth_key');

        $mockCookieService = Mockery::mock(CookieServiceInterface::class);
        $mockCookieService
            ->shouldReceive('forget')
            ->once()
            ->with($tokenKey);

        $mockCookieService
            ->shouldReceive('forget')
            ->once()
            ->with($isAuthKey);

        /** @var \App\Contracts\Repositories\AuthRepositoryInterface $mockRepository */
        $mockRepository = $this->mockRepository;

        /** @var \App\Contracts\Services\CookieServiceInterface $mockCookieService */
        $mockCookieService = $mockCookieService;

        $authService = new AuthService(
            $mockRepository,
            app(CryptServiceInterface::class),
            $mockCookieService,
            app(CacheServiceInterface::class),
        );

        $authService->clearAuthenticationCookies();

        $this->assertEquals(2, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can set the user on request.
     *
     * @return void
     */
    public function test_if_can_set_the_user_on_request(): void
    {
        $user = Mockery::mock(Authenticatable::class);

        $this->mockRepository
            ->shouldReceive('setUser')
            ->once()
            ->with($user);

        /** @var \App\Contracts\Repositories\AuthRepositoryInterface $mockRepository */
        $mockRepository = $this->mockRepository;
        $authService = new AuthService(
            $mockRepository,
            app(CryptServiceInterface::class),
            app(CookieServiceInterface::class),
            app(CacheServiceInterface::class),
        );

        $authService->setUser($user);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can authenticate user by id.
     *
     * @return void
     */
    public function test_if_can_authenticate_user_by_id(): void
    {
        $this->mockRepository
            ->shouldReceive('authenticateById')
            ->once()
            ->with(1);

        /** @var \App\Contracts\Repositories\AuthRepositoryInterface $mockRepository */
        $mockRepository = $this->mockRepository;
        $authService = new AuthService(
            $mockRepository,
            app(CryptServiceInterface::class),
            app(CookieServiceInterface::class),
            app(CacheServiceInterface::class),
        );

        $authService->authenticateById(1);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can get auth user data.
     *
     * @return void
     */
    public function test_if_can_get_auth_user_data(): void
    {
        $user = Mockery::mock(User::class);

        $this->mockRepository
            ->shouldReceive('getAuthUser')
            ->once()
            ->andReturn($user);

        /** @var \App\Contracts\Repositories\AuthRepositoryInterface $mockRepository */
        $mockRepository = $this->mockRepository;
        $authService = new AuthService(
            $mockRepository,
            app(CryptServiceInterface::class),
            app(CookieServiceInterface::class),
            app(CacheServiceInterface::class),
        );

        $authService->getAuthUser();

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
    }

    /**
     * Test if can forget the user cache.
     *
     * @return void
     */
    public function test_if_can_forget_the_user_cache(): void
    {
        $userMock = Mockery::mock(User::class)->makePartial();
        $userMock->shouldAllowMockingMethod('setAttribute');
        $userMock->shouldReceive('getAttribute')->with('id')->andReturn(1);

        /** @var \App\Models\User $userMock */
        $mockCacheService = Mockery::mock(CacheServiceInterface::class);
        $mockCacheService->shouldReceive('forget')
            ->once()
            ->with("auth.user.{$userMock->id}")
            ->andReturnTrue();

        /** @var \App\Contracts\Services\CacheServiceInterface $mockCacheService */
        /** @var \App\Contracts\Repositories\AuthRepositoryInterface $mockRepository */
        $mockRepository = $this->mockRepository;
        $authService = new AuthService(
            $mockRepository,
            app(CryptServiceInterface::class),
            app(CookieServiceInterface::class),
            $mockCacheService,
        );

        $authService->forgetAuthUserCache($userMock);

        $this->assertEquals(1, Mockery::getContainer()->mockery_getExpectationCount(), 'Mock expectations executed.');
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

<?php

namespace Tests\Unit\Repositories;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Services\AuthService;
use Illuminate\Contracts\Foundation\Application;
use App\Exceptions\Auth\InvalidIdentifierException;
use App\Contracts\Repositories\AuthRepositoryInterface;
use App\Contracts\Services\{
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
        );

        $authService->setAuthenticationCookies($token);
    }
}

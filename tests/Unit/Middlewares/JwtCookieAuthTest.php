<?php

namespace Tests\Unit\Middlewares;

use Mockery;
use Mockery\MockInterface;
use App\Http\Middleware\JwtCookieAuth;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Exceptions\Auth\InvalidSessionException;
use Illuminate\Contracts\Encryption\DecryptException;
use Tests\Contracts\Middlewares\BaseMiddlewareTesting;
use App\Contracts\Services\{
    LogServiceInterface,
    JWTServiceInterface,
    AuthServiceInterface,
    CryptServiceInterface,
    CacheServiceInterface,
    CookieServiceInterface,
};

class JwtCookieAuthTest extends BaseMiddlewareTesting
{
    /**
     * The cookie service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $cookieService;

    /**
     * The crypt service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $cryptService;

    /**
     * The jwt service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $jwtService;

    /**
     * The cache service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $cacheService;

    /**
     * The auth service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $authService;

    /**
     * The log service.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $logService;

    /**
     * The jwt cookie auth middleware.
     *
     * @return string
     */
    public function middleware(): string
    {
        return JwtCookieAuth::class;
    }

    /**
     * Resolve the middleware instance with dependencies.
     *
     * @return \App\Http\Middleware\JwtCookieAuth
     */
    public function resolveMiddleware(): JwtCookieAuth
    {
        $logService = Mockery::mock(LogServiceInterface::class);
        $this->logService = $logService;

        $jwtService = Mockery::mock(JWTServiceInterface::class);
        $this->jwtService = $jwtService;

        $authService = Mockery::mock(AuthServiceInterface::class);
        $this->authService = $authService;

        $cryptService = Mockery::mock(CryptServiceInterface::class);
        $this->cryptService = $cryptService;

        $cacheService = Mockery::mock(CacheServiceInterface::class);
        $this->cacheService = $cacheService;

        $cookieService = Mockery::mock(CookieServiceInterface::class);
        $this->cookieService = $cookieService;

        /** @var \App\Contracts\Services\LogServiceInterface $logService */
        /** @var \App\Contracts\Services\JWTServiceInterface $jwtService */
        /** @var \App\Contracts\Services\AuthServiceInterface $authService */
        /** @var \App\Contracts\Services\CryptServiceInterface $cryptService */
        /** @var \App\Contracts\Services\CacheServiceInterface $cacheService */
        /** @var \App\Contracts\Services\CookieServiceInterface $cookieService */
        return new JwtCookieAuth(
            $logService,
            $jwtService,
            $authService,
            $cryptService,
            $cacheService,
            $cookieService,
        );
    }

    /**
     * Test if can handles valid token.
     *
     * @return void
     */
    public function test_if_it_can_handles_valid_token(): void
    {
        /** @var string $tokenKey */
        $tokenKey = config('auth.token_key');
        $encryptedToken = 'encrypted_token';
        $token = 'valid_token';
        $sub = 1;
        $user = Mockery::mock(Authenticatable::class);

        $cacheUserKey = "auth.user.{$sub}";

        $this->cookieService
            ->shouldReceive('get')
            ->once()
            ->with($tokenKey)
            ->andReturn($encryptedToken);

        $this->cryptService
            ->shouldReceive('decrypt')
            ->once()
            ->with($encryptedToken)
            ->andReturn($token);

        $this->jwtService
            ->shouldReceive('decode')
            ->once()
            ->with($token)
            ->andReturn([
                'sub' => $sub,
            ]);

        $this->cacheService
            ->shouldReceive('has')
            ->once()
            ->with($cacheUserKey)
            ->andReturnTrue();

        $this->cacheService
            ->shouldReceive('get')
            ->once()
            ->with($cacheUserKey)
            ->andReturn($user);

        $this->authService
            ->shouldReceive('setUser')
            ->once()
            ->with($user);

        /** @var \App\Http\Middleware\JwtCookieAuth $middleware */
        $middleware = $this->middleware;

        $response = $middleware->handle($this->request, $this->next);

        $this->assertEquals('Next middleware', $response->getContent());
    }

    /**
     * Test if can throw exception and clear cookies if missing token.
     *
     * @return void
     */
    public function test_if_can_throw_exception_and_clear_cookies_if_missing_token(): void
    {
        /** @var string $tokenKey */
        $tokenKey = config('auth.token_key');

        $this->cookieService
            ->shouldReceive('get')
            ->once()
            ->with($tokenKey)
            ->andReturnNull();

        $this->authService
            ->shouldReceive('clearAuthenticationCookies')
            ->once();

        $this->expectException(InvalidSessionException::class);

        /** @var \App\Http\Middleware\JwtCookieAuth $middleware */
        $middleware = $this->middleware;

        $middleware->handle($this->request, $this->next);
    }

    /**
     * Test if can throw exception if decrypt fails.
     *
     * @return void
     */
    public function test_if_can_throw_exception_if_decrypt_fails(): void
    {
        /** @var string $tokenKey */
        $tokenKey = config('auth.token_key');
        $token = 'invalid_token';

        $this->cookieService
            ->shouldReceive('get')
            ->with($tokenKey)
            ->andReturn($token);

        $this->cryptService
            ->shouldReceive('decrypt')
            ->with($token)
            ->andThrow(DecryptException::class);

        $this->authService
            ->shouldReceive('clearAuthenticationCookies')
            ->once();

        $this->logService
            ->shouldReceive('error')
            ->once();

        $this->expectException(InvalidSessionException::class);

        /** @var \App\Http\Middleware\JwtCookieAuth $middleware */
        $middleware = $this->middleware;

        $middleware->handle($this->request, $this->next);
    }

    /**
     * Tear down the mocks.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}

<?php

namespace Tests\Unit\Middlewares;

use Mockery;
use App\Models\User;
use Mockery\MockInterface;
use App\Http\Middleware\MidJwtCookieAuth;
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

class MidJwtCookieAuthTest extends BaseMiddlewareTesting
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
        return MidJwtCookieAuth::class;
    }

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->logService = Mockery::mock(LogServiceInterface::class);
        $this->jwtService = Mockery::mock(JWTServiceInterface::class);
        $this->authService = Mockery::mock(AuthServiceInterface::class);
        $this->cryptService = Mockery::mock(CryptServiceInterface::class);
        $this->cacheService = Mockery::mock(CacheServiceInterface::class);
        $this->cookieService = Mockery::mock(CookieServiceInterface::class);

        $this->app->instance(LogServiceInterface::class, $this->logService);
        $this->app->instance(JWTServiceInterface::class, $this->jwtService);
        $this->app->instance(AuthServiceInterface::class, $this->authService);
        $this->app->instance(CryptServiceInterface::class, $this->cryptService);
        $this->app->instance(CacheServiceInterface::class, $this->cacheService);
        $this->app->instance(CookieServiceInterface::class, $this->cookieService);
    }

    /**
     * Resolve the middleware instance with dependencies.
     *
     * @return \App\Http\Middleware\MidJwtCookieAuth
     */
    public function resolveMiddleware(): MidJwtCookieAuth
    {
        return new MidJwtCookieAuth();
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

        $user = new User();
        $user->id = $sub;

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
            ->andReturn(['sub' => $sub]);

        $this->cacheService
            ->shouldReceive('has')
            ->once()
            ->with($cacheUserKey)
            ->andReturnTrue();

        $this->cacheService
            ->shouldReceive('get')
            ->once()
            ->with($cacheUserKey)
            ->andReturn(base64_encode(serialize($user)));

        $this->authService
            ->shouldReceive('setUser')
            ->once()
            ->with(Mockery::on(fn (User $u) => $u->id === $sub));

        $response = $this->resolveMiddleware()->handle($this->request, $this->next);

        $this->assertEquals('Next middleware', $response->getContent());
    }

    /**
     * Test if can authenticate user by id if cache doesn't exist.
     *
     * @return void
     */
    public function test_if_can_authenticate_user_by_id_if_cache_doesnt_exist(): void
    {
        /** @var string $tokenKey */
        $tokenKey = config('auth.token_key');
        $encryptedToken = 'encrypted_token';
        $token = 'valid_token';
        $sub = 1;

        $user = new User();
        $user->id = $sub;

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
            ->andReturn(['sub' => $sub]);

        $this->cacheService
            ->shouldReceive('has')
            ->once()
            ->with($cacheUserKey)
            ->andReturnFalse();

        $this->cacheService->shouldNotReceive('get');

        $this->authService->shouldNotReceive('setUser');

        $this->authService
            ->shouldReceive('authenticateById')
            ->once()
            ->with($sub)
            ->andReturnNull();

        $response = $this->resolveMiddleware()->handle($this->request, $this->next);

        $this->assertEquals('Next middleware', $response->getContent());
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
            ->once()
            ->with($tokenKey)
            ->andReturn($token);

        $this->cryptService
            ->shouldReceive('decrypt')
            ->once()
            ->with($token)
            ->andThrow(DecryptException::class);

        $this->authService
            ->shouldReceive('clearAuthenticationCookies')
            ->once();

        $this->logService
            ->shouldReceive('error')
            ->once();

        $this->expectException(InvalidSessionException::class);

        $this->resolveMiddleware()->handle($this->request, $this->next);
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

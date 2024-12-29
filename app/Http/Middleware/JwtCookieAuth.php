<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\Auth\InvalidSessionException;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Contracts\Services\{
    JWTServiceInterface,
    LogServiceInterface,
    AuthServiceInterface,
    CryptServiceInterface,
    CacheServiceInterface,
    CookieServiceInterface,
};

class JwtCookieAuth
{
    /**
     * The cookie service.
     *
     * @var \App\Contracts\Services\CookieServiceInterface
     */
    private CookieServiceInterface $cookieService;

    /**
     * The cache service.
     *
     * @var \App\Contracts\Services\CacheServiceInterface
     */
    private CacheServiceInterface $cacheService;

    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * The crypt service.
     *
     * @var \App\Contracts\Services\CryptServiceInterface
     */
    private CryptServiceInterface $cryptService;

    /**
     * The JWT service.
     *
     * @var \App\Contracts\Services\JWTServiceInterface
     */
    private JWTServiceInterface $jwtService;

    /**
     * The log service.
     *
     * @var \App\Contracts\Services\LogServiceInterface
     */
    private LogServiceInterface $logService;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Services\LogServiceInterface $logService
     * @param \App\Contracts\Services\JWTServiceInterface $jwtService
     * @param \App\Contracts\Services\AuthServiceInterface $authService
     * @param \App\Contracts\Services\CryptServiceInterface $cryptService
     * @param \App\Contracts\Services\CacheServiceInterface $cacheService
     * @param \App\Contracts\Services\CookieServiceInterface $cookieService
     * @return void
     */
    public function __construct(
        LogServiceInterface $logService,
        JWTServiceInterface $jwtService,
        AuthServiceInterface $authService,
        CryptServiceInterface $cryptService,
        CacheServiceInterface $cacheService,
        CookieServiceInterface $cookieService,
    ) {
        $this->logService = $logService;
        $this->jwtService = $jwtService;
        $this->authService = $authService;
        $this->cryptService = $cryptService;
        $this->cacheService = $cacheService;
        $this->cookieService = $cookieService;
    }

    /**
     * Handle an incoming request.
     *
     * @throws \App\Exceptions\Auth\InvalidUserException
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var string $tokenKey */
        $tokenKey = config('auth.token_key');

        $token = $this->cookieService->get($tokenKey);

        if (!$token) {
            $this->authService->clearAuthenticationCookies();

            throw new InvalidSessionException();
        }

        try {
            /** @var string $decrypted */
            $decrypted = $this->cryptService->decrypt($token);
        } catch (DecryptException $e) {
            $this->logService->error(
                'Failed to decrypt token.',
                $e->getMessage(),
                $e->getTraceAsString(),
            );

            $this->authService->clearAuthenticationCookies();

            throw new InvalidSessionException();
        }

        /** @var array<string, mixed> $claims */
        $claims = $this->jwtService->decode($decrypted);

        /** @var string $sub */
        $sub = $claims['sub'];

        $key = "auth.user.{$sub}";

        if ($this->cacheService->has($key)) {
            /** @var non-empty-string $userData */
            $userData = $this->cacheService->get($key);

            /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
            $user = unserialize(base64_decode($userData));

            $this->authService->setUser($user);
        } else {
            $this->authService->authenticateById($sub);
        }

        return $next($request);
    }
}

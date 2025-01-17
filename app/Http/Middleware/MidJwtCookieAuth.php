<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\Auth\InvalidSessionException;
use App\Contracts\Services\{
    JWTServiceInterface,
    AuthServiceInterface,
    CryptServiceInterface,
    CacheServiceInterface,
    CookieServiceInterface,
};

class MidJwtCookieAuth
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
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->jwtService = app(JWTServiceInterface::class);
        $this->authService = app(AuthServiceInterface::class);
        $this->cryptService = app(CryptServiceInterface::class);
        $this->cacheService = app(CacheServiceInterface::class);
        $this->cookieService = app(CookieServiceInterface::class);
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

        if ($token) {
            try {
                /** @var string $decrypted */
                $decrypted = $this->cryptService->decrypt($token);

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
            } catch (Exception $e) {
                $this->authService->clearAuthenticationCookies();

                logService()->error('Failed to authenticate user.', $e->getMessage(), $e->getTraceAsString());

                throw new InvalidSessionException();
            }
        }

        return $next($request);
    }
}

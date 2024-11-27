<?php

namespace App\Services;

use App\Models\User;
use App\Exceptions\Auth\InvalidIdentifierException;
use App\Contracts\Repositories\AuthRepositoryInterface;
use App\Contracts\Services\{
    AuthServiceInterface,
    CacheServiceInterface,
    CryptServiceInterface,
    CookieServiceInterface,
    Validation\IdentifierValidatorInterface,
};

class AuthService implements AuthServiceInterface
{
    /**
     * The auth repository interface.
     *
     * @var \App\Contracts\Repositories\AuthRepositoryInterface
     */
    private AuthRepositoryInterface $authRepository;

    /**
     * The email identifier validator.
     *
     * @var \App\Contracts\Services\Validation\IdentifierValidatorInterface
     */
    private $emailValidator;

    /**
     * The nickname identifier validator.
     *
     * @var \App\Contracts\Services\Validation\IdentifierValidatorInterface
     */
    private $nicknameValidator;

    /**
     * The crypt service.
     *
     * @var \App\Contracts\Services\CryptServiceInterface
     */
    private CryptServiceInterface $cryptService;

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
     * Create a new class instance.
     *
     * @param \App\Contracts\Repositories\AuthRepositoryInterface $authRepository
     * @param \App\Contracts\Services\CryptServiceInterface $cryptService
     * @param \App\Contracts\Services\CookieServiceInterface $cookieService
     * @param \App\Contracts\Services\CacheServiceInterface $cacheService
     * @return void
     */
    public function __construct(
        AuthRepositoryInterface $authRepository,
        CryptServiceInterface $cryptService,
        CookieServiceInterface $cookieService,
        CacheServiceInterface $cacheService,
    ) {
        $this->cryptService = $cryptService;
        $this->cacheService = $cacheService;
        $this->cookieService = $cookieService;
        $this->authRepository = $authRepository;
        $this->emailValidator = app(IdentifierValidatorInterface::class, ['type' => 'email']);
        $this->nicknameValidator = app(IdentifierValidatorInterface::class, ['type' => 'nickname']);
    }

    /**
     * Authenticates the user on platform based on credentials.
     *
     * @param array<string, string> $credentials
     * @return string
     */
    public function auth(array $credentials): string
    {
        $identifier = $credentials['identifier'];

        if ($this->emailValidator->validate($identifier)) {
            return $this->authRepository->authByEmail($credentials);
        } elseif ($this->nicknameValidator->validate($identifier)) {
            return $this->authRepository->authByNickname($credentials);
        }

        throw new InvalidIdentifierException();
    }

    /**
     * Set authentication cookies for a given token.
     *
     * @param string $token
     * @return void
     */
    public function setAuthenticationCookies(string $token): void
    {
        /** @var string $tokenKey*/
        $tokenKey = config('auth.token_key');
        /** @var int $ttl */
        $ttl = config('jwt.ttl');
        /** @var string $path */
        $path = config('session.path');
        /** @var string $domain */
        $domain = config('session.domain');
        /** @var bool $secure */
        $secure = config('session.secure');
        /** @var bool $httpOnly */
        $httpOnly = config('session.http_only');

        $this->cookieService->queue(
            $tokenKey,
            $this->cryptService->encrypt($token),
            $ttl,
            $path,
            $domain,
            $secure,
            $httpOnly,
        );

        /** @var string $tokenKey */
        $tokenKey = config('auth.is_auth_key');

        $this->cookieService->queue(
            $tokenKey,
            '1',
            $ttl,
            $path,
            $domain,
            false,
            false,
        );
    }

    /**
     * Clear the auth cookies.
     *
     * @return void
     */
    public function clearAuthenticationCookies(): void
    {
        /** @var string $tokenKey*/
        $tokenKey = config('auth.token_key');

        /** @var string $isAuthKey */
        $isAuthKey = config('auth.is_auth_key');

        $this->cookieService->forget($tokenKey);
        $this->cookieService->forget($isAuthKey);
    }

    /**
     * Get the authenticated user id.
     *
     * @return mixed
     */
    public function getAuthId(): mixed
    {
        return $this->authRepository->getAuthId();
    }

    /**
     * Set the authenticated user on request.
     *
     * @param mixed $user
     * @return void
     */
    public function setUser(mixed $user): void
    {
        $this->authRepository->setUser($user);
    }

    /**
     * Authenticate user by his id.
     *
     * @param mixed $id
     * @return void
     */
    public function authenticateById(mixed $id): void
    {
        $this->authRepository->authenticateById($id);
    }

    /**
     * Get the authenticated user.
     *
     * @return \App\Models\User
     */
    public function getAuthUser(): User
    {
        return $this->authRepository->getAuthUser();
    }

    /**
     * Forget user cache.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function forgetAuthUserCache(User $user): void
    {
        $key = "auth.user.{$user->id}";

        $this->cacheService->forget($key);
    }
}

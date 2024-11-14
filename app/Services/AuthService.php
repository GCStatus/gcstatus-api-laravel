<?php

namespace App\Services;

use App\Exceptions\Auth\InvalidIdentifierException;
use App\Contracts\Repositories\AuthRepositoryInterface;
use App\Contracts\Services\{
    AuthServiceInterface,
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
    private $authRepository;

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
     * Create a new class instance.
     *
     * @param \App\Contracts\Repositories\AuthRepositoryInterface $authRepository
     * @return void
     */
    public function __construct(
        AuthRepositoryInterface $authRepository,
        CryptServiceInterface $cryptService,
        CookieServiceInterface $cookieService,
    ) {
        $this->cryptService = $cryptService;
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
     * Get the authenticated user id.
     *
     * @return mixed
     */
    public function getAuthId(): mixed
    {
        return $this->authRepository->getAuthId();
    }
}

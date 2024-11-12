<?php

namespace App\Services;

use App\Contracts\Services\CookieServiceInterface;
use App\Contracts\Repositories\CookieRepositoryInterface;

class CookieService implements CookieServiceInterface
{
    /**
     * The cookie repository.
     *
     * @var \App\Contracts\Repositories\CookieRepositoryInterface
     */
    private $cookieRepository;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Repositories\CookieRepositoryInterface $cookieRepository
     * @return void
     */
    public function __construct(CookieRepositoryInterface $cookieRepository)
    {
        $this->cookieRepository = $cookieRepository;
    }

    /**
     * Add a new cookie enqueued to storage.
     *
     * @param string $key
     * @param string $value
     * @param int $minutes
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httpOnly
     * @return void
     */
    public function queue(string $key, string $value, int $minutes, string $path, string $domain, bool $secure, bool $httpOnly): void
    {
        $this->cookieRepository->queue($key, $value, $minutes, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Forget cookie from storage.
     *
     * @param string $key
     * @return void
     */
    public function forget(string $key): void
    {
        $this->cookieRepository->forget($key);
    }

    /**
     * Check has cookie on storage.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->cookieRepository->has($key);
    }

    /**
     * Get the cookie from storage.
     *
     * @param string $key
     * @return ?string
     */
    public function get(string $key): ?string
    {
        return $this->cookieRepository->get($key);
    }
}

<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cookie;
use App\Contracts\Repositories\CookieRepositoryInterface;

class CookieRepository implements CookieRepositoryInterface
{
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
        Cookie::queue($key, $value, $minutes, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Forget cookie from storage.
     *
     * @param string $key
     * @return void
     */
    public function forget(string $key): void
    {
        Cookie::queue(Cookie::forget($key));
    }

    /**
     * Check has cookie on storage.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return Cookie::has($key) ||
            collect(Cookie::getQueuedCookies())->contains(fn ($cookie) => $cookie->getName() === $key);
    }

    /**
     * Get the cookie from storage.
     *
     * @param string $key
     * @return ?string
     */
    public function get(string $key): ?string
    {
        /** @var string $cookie */
        $cookie = Cookie::get($key) ??
            collect(Cookie::getQueuedCookies())->filter(fn ($cookie) => $cookie->getName() === $key)->first()?->getValue();

        return $cookie;
    }
}

<?php

namespace App\Contracts\Services;

interface CookieServiceInterface
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
    public function queue(string $key, string $value, int $minutes, string $path, string $domain, bool $secure, bool $httpOnly): void;

    /**
     * Forget cookie from storage.
     *
     * @param string $key
     * @return void
     */
    public function forget(string $key): void;

    /**
     * Check has cookie on storage.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Get the cookie from storage.
     *
     * @param string $key
     * @return ?string
     */
    public function get(string $key): ?string;
}

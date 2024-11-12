<?php

namespace App\Contracts\Services;

interface AuthServiceInterface
{
    /**
     * Authenticate users on platform based on credentials.
     *
     * @param array<string, string> $credentials
     * @return string
     */
    public function auth(array $credentials): string;

    /**
     * Enqueue auth cookies to response.
     *
     * @param string $token
     * @return void
     */
    public function setAuthenticationCookies(string $token): void;
}

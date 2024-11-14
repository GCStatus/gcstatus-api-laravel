<?php

namespace App\Contracts\Repositories;

interface AuthRepositoryInterface
{
    /**
     * Authenticate users on platform based on credentials.
     *
     * @param array<string, string> $credentials
     * @return string
     */
    public function authByEmail(array $credentials): string;

    /**
     * Authenticate users on platform based on credentials.
     *
     * @param array<string, string> $credentials
     * @return string
     */
    public function authByNickname(array $credentials): string;

    /**
     * Get the authenticated user id.
     *
     * @return mixed
     */
    public function getAuthId(): mixed;
}

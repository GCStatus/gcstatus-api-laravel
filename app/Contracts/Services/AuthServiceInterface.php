<?php

namespace App\Contracts\Services;

use App\Models\User;

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

    /**
     * Clear the authentication cookies.
     *
     * @return void
     */
    public function clearAuthenticationCookies(): void;

    /**
     * Get the authenticated user id.
     *
     * @return mixed
     */
    public function getAuthId(): mixed;

    /**
     * Set the authenticated user on request.
     *
     * @param mixed $user
     * @return void
     */
    public function setUser(mixed $user): void;

    /**
     * Authenticate user by his id.
     *
     * @param mixed $id
     * @return void
     */
    public function authenticateById(mixed $id): void;

    /**
     * Get the authenticated user.
     *
     * @return \App\Models\User
     */
    public function getAuthUser(): User;

    /**
     * Forget auth user cache.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function forgetAuthUserCache(User $user): void;
}

<?php

namespace App\Contracts\Repositories;

use App\Models\User;

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
}

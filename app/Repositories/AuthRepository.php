<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Contracts\Repositories\AuthRepositoryInterface;

class AuthRepository implements AuthRepositoryInterface
{
    /**
     * Authenticates user on platform based on email.
     *
     * @param array<string, string> $credentials
     * @return string
     */
    public function authByEmail(array $credentials): string
    {
        return (string)Auth::attempt([
            'email' => $credentials['identifier'],
            'password' => $credentials['password'],
        ]);
    }

    /**
     * Authenticates user on platform based on nickname.
     *
     * @param array<string, string> $credentials
     * @return string
     */
    public function authByNickname(array $credentials): string
    {
        return (string)Auth::attempt([
            'nickname' => $credentials['identifier'],
            'password' => $credentials['password'],
        ]);
    }

    /**
     * Get the authenticated user data.
     *
     * @return \App\Models\User
     */
    public function getAuthUser(): User
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user;
    }

    /**
     * Get the authenticated user id.
     *
     * @return mixed
     */
    public function getAuthId(): mixed
    {
        return Auth::id();
    }

    /**
     * Set the authenticated user on request.
     *
     * @param mixed $user
     * @return void
     */
    public function setUser(mixed $user): void
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable $user */
        Auth::setUser($user);
    }

    /**
     * Authenticate user by his id.
     *
     * @param mixed $id
     * @return void
     */
    public function authenticateById(mixed $id): void
    {
        Auth::onceUsingId($id);
    }
}

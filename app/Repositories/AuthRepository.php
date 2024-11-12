<?php

namespace App\Repositories;

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
}

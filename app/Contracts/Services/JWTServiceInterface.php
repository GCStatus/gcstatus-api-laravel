<?php

namespace App\Contracts\Services;

use App\Models\User;

interface JWTServiceInterface
{
    /**
     * Generates a JWT token from user.
     *
     * @param \App\Models\User $user
     * @return string
     */
    public function tokenize(User $user): string;

    /**
     * Decode the jwt token.
     *
     * @param string $token
     * @return array<string, mixed>
     */
    public function decode(string $token): array;
}

<?php

namespace App\Contracts\Repositories;

use App\Models\User;

interface JWTRepositoryInterface
{
    /**
     * Generates a JWT token from user.
     *
     * @param \App\Models\User $user
     * @return string
     */
    public function tokenize(User $user): string;
}

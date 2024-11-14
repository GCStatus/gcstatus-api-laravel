<?php

namespace App\Services;

use App\Models\User;
use App\Contracts\Services\JWTServiceInterface;
use App\Contracts\Repositories\JWTRepositoryInterface;

class JWTService implements JWTServiceInterface
{
    /**
     * The JWT lib helper.
     *
     * @var \App\Contracts\Repositories\JWTRepositoryInterface
     */
    private $jwtRepository;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->jwtRepository = app(JWTRepositoryInterface::class);
    }

    /**
     * Generates a JWT token from user.
     *
     * @param \App\Models\User $user
     * @return string
     */
    public function tokenize(User $user): string
    {
        return $this->jwtRepository->tokenize($user);
    }
}

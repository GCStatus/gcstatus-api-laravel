<?php

namespace App\Repositories;

use App\Models\User;
use Tymon\JWTAuth\JWT;
use App\Contracts\Repositories\JWTRepositoryInterface;

class JWTRepository implements JWTRepositoryInterface
{
    /**
     * The JWT lib helper.
     *
     * @var \Tymon\JWTAuth\JWT
     */
    private $jwt;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->jwt = app(JWT::class);
    }

    /**
     * Generates a JWT token from user.
     *
     * @param \App\Models\User $user
     * @return string
     */
    public function tokenize(User $user): string
    {
        return $this->jwt->fromUser($user);
    }
}

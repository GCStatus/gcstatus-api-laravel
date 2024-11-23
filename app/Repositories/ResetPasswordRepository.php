<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use App\Contracts\Repositories\ResetPasswordRepositoryInterface;

class ResetPasswordRepository implements ResetPasswordRepositoryInterface
{
    /**
     * The token repository interface.
     *
     * @var \Illuminate\Auth\Passwords\TokenRepositoryInterface
     */
    private $tokenRepository;

    /**
     * Create new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->tokenRepository = app(TokenRepositoryInterface::class);
    }

    /**
     * Create reset token.
     *
     * @param \App\Models\User $user
     * @return string
     */
    public function createResetToken(User $user): string
    {
        return $this->tokenRepository->create($user);
    }

    /**
     * Check if user recently created a token.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function recentlyCreatedToken(User $user): bool
    {
        return $this->tokenRepository->recentlyCreatedToken($user);
    }

    /**
     * Check if exists a token for given user.
     *
     * @param \App\Models\User $user
     * @param string $token
     * @return bool
     */
    public function exists(User $user, string $token): bool
    {
        return $this->tokenRepository->exists($user, $token);
    }

    /**
     * Delete token for given user.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function delete(User $user): void
    {
        $this->tokenRepository->delete($user);
    }
}

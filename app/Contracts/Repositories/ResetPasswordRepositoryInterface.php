<?php

namespace App\Contracts\Repositories;

use App\Models\User;

interface ResetPasswordRepositoryInterface
{
    /**
     * Create reset token.
     *
     * @param \App\Models\User $user
     * @return string
     */
    public function createResetToken(User $user): string;

    /**
     * Check if user recently created a token.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function recentlyCreatedToken(User $user): bool;

    /**
     * Check if exists a token for given user.
     *
     * @param \App\Models\User $user
     * @param string $token
     * @return bool
     */
    public function exists(User $user, string $token): bool;

    /**
     * Delete token for given user.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function delete(User $user): void;
}

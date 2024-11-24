<?php

namespace App\Contracts\Services;

use App\Models\User;

interface EmailVerifyServiceInterface
{
    /**
     * Check if user already verified email.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function verified(User $user): bool;

    /**
     * Mark user email as verified.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function verify(User $user): bool;

    /**
     * Notify user of email verification.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function notify(User $user): void;
}

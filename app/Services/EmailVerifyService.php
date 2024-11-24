<?php

namespace App\Services;

use App\Models\User;
use App\Contracts\Services\EmailVerifyServiceInterface;
use App\Exceptions\EmailVerify\AlreadyVerifiedEmailException;

class EmailVerifyService implements EmailVerifyServiceInterface
{
    /**
     * Check if user already verified email.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function verified(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    /**
     * Mark user email as verified.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function verify(User $user): bool
    {
        if (!$this->verified($user)) {
            return $user->markEmailAsVerified();
        }

        throw new AlreadyVerifiedEmailException();
    }

    /**
     * Notify user of email verification.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function notify(User $user): void
    {
        if ($this->verified($user)) {
            throw new AlreadyVerifiedEmailException();
        }

        $user->sendEmailVerificationNotification();
    }
}

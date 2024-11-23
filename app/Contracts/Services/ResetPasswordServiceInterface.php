<?php

namespace App\Contracts\Services;

interface ResetPasswordServiceInterface
{
    /**
     * Send reset notification.
     *
     * @param array<string, string> $data
     * @return void
     */
    public function sendResetNotification(array $data): void;

    /**
     * Reset suer password.
     *
     * @param array<string, string> $data
     * @return void
     */
    public function resetPassword(array $data): void;
}

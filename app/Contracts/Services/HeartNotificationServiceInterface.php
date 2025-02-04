<?php

namespace App\Contracts\Services;

use App\Models\Heartable;

interface HeartNotificationServiceInterface
{
    /**
     * Notify the user about a new heart.
     *
     * @param \App\Models\Heartable $heartable
     * @return void
     */
    public function notifyNewHeart(Heartable $heartable): void;
}

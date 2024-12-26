<?php

namespace App\Contracts\Services;

use App\Models\{User, Level};

interface LevelNotificationServiceInterface
{
    /**
     * Notify the user gained experience.
     *
     * @param \App\Models\User $user
     * @param int $amount
     * @return void
     */
    public function notifyExperienceGained(User $user, int $amount): void;

    /**
     * Notify the user level up.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Level $level
     * @return void
     */
    public function notifyLevelUp(User $user, Level $level): void;
}

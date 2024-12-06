<?php

namespace App\Contracts\Services;

use App\Models\{User, Mission};

interface UserMissionServiceInterface
{
    /**
     * Mark the given mission as completed for given user.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Mission $mission
     * @return void
     */
    public function markMissionComplete(User $user, Mission $mission): void;

    /**
     * Check if user already completed mission.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Mission $mission
     * @return bool
     */
    public function userAlreadyCompletedMission(User $user, Mission $mission): bool;
}

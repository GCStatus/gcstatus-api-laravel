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
}

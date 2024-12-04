<?php

namespace App\Contracts\Services;

use App\Models\{User, MissionRequirement};

interface UserMissionProgressServiceInterface
{
    /**
     * Update progress for a specific requirement.
     *
     * @param \App\Models\User $user
     * @param \App\Models\MissionRequirement $requirement
     * @return void
     */
    public function updateProgress(User $user, MissionRequirement $requirement): void;
}

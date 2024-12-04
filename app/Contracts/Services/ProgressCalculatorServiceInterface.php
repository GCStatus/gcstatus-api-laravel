<?php

namespace App\Contracts\Services;

use App\Models\{User, Mission, MissionRequirement};

interface ProgressCalculatorServiceInterface
{
    /**
     * Determine the progress for a given mission requirement.
     *
     * @param \App\Models\User $user
     * @param \App\Models\MissionRequirement $requirement
     * @return int
     */
    public function determineProgress(User $user, MissionRequirement $requirement): int;

    /**
     * Check if requirement is complete.
     *
     * @param \App\Models\User $user
     * @param \App\Models\MissionRequirement $requirement
     * @return bool
     */
    public function isRequirementComplete(User $user, MissionRequirement $requirement): bool;

    /**
     * Check if mission is complete.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Mission $mission
     * @return bool
     */
    public function isMissionComplete(User $user, Mission $mission): bool;
}

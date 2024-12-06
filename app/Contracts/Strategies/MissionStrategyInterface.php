<?php

namespace App\Contracts\Strategies;

use App\Models\{User, MissionRequirement};

interface MissionStrategyInterface
{
    /**
     * Calculate progress for a mission requirement.
     *
     * @param \App\Models\User $user
     * @param \App\Models\MissionRequirement $missionRequirement
     * @return int
     */
    public function calculateProgress(User $user, MissionRequirement $missionRequirement): int;
}

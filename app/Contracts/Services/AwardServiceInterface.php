<?php

namespace App\Contracts\Services;

use App\Models\{User, Mission};

interface AwardServiceInterface
{
    /**
     * Award the mission rewards.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Mission $mission
     * @return void
     */
    public function awardRewards(User $user, Mission $mission): void;

    /**
     * Awaird mission coins and experience for user.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Mission $mission
     * @return void
     */
    public function awardCoinsAndExperience(User $user, Mission $mission): void;

    /**
     * Handle the mission completion.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Mission $mission
     * @return void
     */
    public function handleMissionCompletion(User $user, Mission $mission): void;
}

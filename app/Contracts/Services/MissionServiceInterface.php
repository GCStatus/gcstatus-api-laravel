<?php

namespace App\Contracts\Services;

use App\Models\{User, Mission};
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MissionServiceInterface
{
    /**
     * Get all missions for user.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Models\Mission>
     */
    public function allForUser(): LengthAwarePaginator;

    /**
     * Complete some given mission for user.
     *
     * @param mixed $id
     * @return void
     */
    public function complete(mixed $id): void;

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

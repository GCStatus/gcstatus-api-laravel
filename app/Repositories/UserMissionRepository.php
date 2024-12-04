<?php

namespace App\Repositories;

use App\Models\UserMission;
use App\Contracts\Repositories\UserMissionRepositoryInterface;

class UserMissionRepository implements UserMissionRepositoryInterface
{
    /**
     * Update or create the user mission.
     *
     * @param array<string, mixed> $verifiable
     * @param array<string, mixed> $updatable
     * @return void
     */
    public function updateOrCreate(array $verifiable, array $updatable): void
    {
        UserMission::updateOrCreate($verifiable, $updatable);
    }
}

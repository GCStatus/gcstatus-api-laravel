<?php

namespace App\Repositories;

use App\Models\UserMission;
use App\Contracts\Repositories\UserMissionRepositoryInterface;

class UserMissionRepository implements UserMissionRepositoryInterface
{
    /**
     * Check if user already completed mission.
     *
     * @param mixed $userId
     * @param mixed $missionId
     * @return bool
     */
    public function userAlreadyCompletedMission(mixed $userId, mixed $missionId): bool
    {
        return UserMission::query()
            ->where('user_id', $userId)
            ->where('mission_id', $missionId)
            ->where('completed', true)
            ->exists();
    }

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

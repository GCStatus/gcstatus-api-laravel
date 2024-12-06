<?php

namespace App\Contracts\Repositories;

interface UserMissionRepositoryInterface
{
    /**
     * Update or create the user mission.
     *
     * @param array<string, mixed> $verifiable
     * @param array<string, mixed> $updatable
     * @return void
     */
    public function updateOrCreate(array $verifiable, array $updatable): void;

    /**
     * Check if user already completed mission.
     *
     * @param mixed $userId
     * @param mixed $missionId
     * @return bool
     */
    public function userAlreadyCompletedMission(mixed $userId, mixed $missionId): bool;
}

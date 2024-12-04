<?php

namespace App\Repositories;

use App\Models\UserMissionProgress;
use App\Contracts\Repositories\UserMissionProgressRepositoryInterface;

class UserMissionProgressRepository implements UserMissionProgressRepositoryInterface
{
    /**
     * Update or create a given progress.
     *
     * @param array<string, mixed> $verifiable
     * @param array<string, mixed> $updatable
     * @return void
     */
    public function updateOrCreate(array $verifiable, array $updatable): void
    {
        UserMissionProgress::updateOrCreate($verifiable, $updatable);
    }
}

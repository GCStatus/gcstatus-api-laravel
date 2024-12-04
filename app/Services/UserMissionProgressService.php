<?php

namespace App\Services;

use App\Contracts\Repositories\UserMissionProgressRepositoryInterface;
use App\Models\{User, MissionRequirement};
use App\Contracts\Services\UserMissionProgressServiceInterface;

class UserMissionProgressService implements UserMissionProgressServiceInterface
{
    /**
     * The user mission progress repository.
     *
     * @var \App\Contracts\Repositories\UserMissionProgressRepositoryInterface
     */
    private UserMissionProgressRepositoryInterface $userMissionProgressRepository;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Repositories\UserMissionProgressRepositoryInterface $userMissionProgressRepository
     * @return void
     */
    public function __construct(UserMissionProgressRepositoryInterface $userMissionProgressRepository)
    {
        $this->userMissionProgressRepository = $userMissionProgressRepository;
    }

    /**
     * Update progress for a specific requirement.
     *
     * @param \App\Models\User $user
     * @param \App\Models\MissionRequirement $requirement
     * @return void
     */
    public function updateProgress(User $user, MissionRequirement $requirement): void
    {
        $progress = progressCalculator()->determineProgress($user, $requirement);

        $completed = $progress >= $requirement->goal;

        $this->userMissionProgressRepository->updateOrCreate(
            ['user_id' => $user->id, 'mission_requirement_id' => $requirement->id],
            ['progress' => $progress, 'completed' => $completed]
        );
    }
}

<?php

namespace App\Services;

use App\Models\{User, Mission};
use App\Contracts\Services\UserMissionServiceInterface;
use App\Contracts\Repositories\UserMissionRepositoryInterface;

class UserMissionService implements UserMissionServiceInterface
{
    /**
     * The user mission repository.
     *
     * @var \App\Contracts\Repositories\UserMissionRepositoryInterface
     */
    private UserMissionRepositoryInterface $userMissionRepository;

    /**
     * Create new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->userMissionRepository = app(UserMissionRepositoryInterface::class);
    }

    /**
     * Mark the given mission as completed for given user.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Mission $mission
     * @return void
     */
    public function markMissionComplete(User $user, Mission $mission): void
    {
        $this->userMissionRepository->updateOrCreate([
            'user_id' => $user->id,
            'mission_id' => $mission->id,
        ], [
            'completed' => true,
            'last_completed_at' => now(),
        ]);
    }
}

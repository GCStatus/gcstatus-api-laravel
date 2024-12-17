<?php

namespace App\Services;

use App\Jobs\CompleteMissionJob;
use App\Models\{User, Mission, Status};
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Contracts\Repositories\MissionRepositoryInterface;
use App\Contracts\Services\{
    AuthServiceInterface,
    MissionServiceInterface,
};
use App\Exceptions\Mission\{
    MissionIsNotCompletedException,
    MissionIsNotAvailableException,
    UserDoesntBelongsToMissionException,
};

class MissionService implements MissionServiceInterface
{
    /**
     * The mission repository.
     *
     * @var \App\Contracts\Repositories\MissionRepositoryInterface
     */
    private MissionRepositoryInterface $missionRepository;

    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authService = app(AuthServiceInterface::class);
        $this->missionRepository = app(MissionRepositoryInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function allForUser(): LengthAwarePaginator
    {
        /** @var \App\Models\User $user */
        $user = $this->authService->getAuthUser();

        return $this->missionRepository->allForUser($user);
    }

    /**
     * @inheritDoc
     */
    public function complete(mixed $id): void
    {
        /** @var \App\Models\User $user */
        $user = $this->authService->getAuthUser();

        /** @var \App\Models\Mission $mission */
        $mission = $this->missionRepository->findOrFail($id);

        $this->assertCanCompleteMission($user, $mission);

        CompleteMissionJob::dispatchSync($user, $mission);
    }

    /**
     * Assert user can complete given mission.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Mission $mission
     * @throws \App\Exceptions\Mission\MissionIsNotAvailableException
     * @throws \App\Exceptions\Mission\MissionIsNotCompletedException
     * @throws \App\Exceptions\Mission\UserDoesntBelongsToMissionException
     * @return void
     */
    private function assertCanCompleteMission(User $user, Mission $mission): void
    {
        if (in_array((int)$mission->status_id, [Status::UNAVAILABLE_STATUS_ID])) {
            throw new MissionIsNotAvailableException();
        }

        $mission->load('users');

        if (!$mission->for_all && $mission->users->doesntContain('pivot.user_id', $user->id)) {
            throw new UserDoesntBelongsToMissionException();
        }

        if (!progressCalculator()->isMissionComplete($user, $mission)) {
            throw new MissionIsNotCompletedException();
        }
    }
}

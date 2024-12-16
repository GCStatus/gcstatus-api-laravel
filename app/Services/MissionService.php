<?php

namespace App\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Contracts\Repositories\MissionRepositoryInterface;
use App\Contracts\Services\{
    AuthServiceInterface,
    MissionServiceInterface,
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
}

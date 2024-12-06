<?php

namespace App\Services;

use App\Models\{User, Mission};
use App\Contracts\Services\{
    UserServiceInterface,
    AwardServiceInterface,
    WalletServiceInterface,
    UserMissionServiceInterface,
};

class AwardService implements AwardServiceInterface
{
    /**
     * The user service.
     *
     * @var \App\Contracts\Services\UserServiceInterface
     */
    private UserServiceInterface $userService;

    /**
     * The wallet service.
     *
     * @var \App\Contracts\Services\WalletServiceInterface
     */
    private WalletServiceInterface $walletService;

    /**
     * The user mission service.
     *
     * @var \App\Contracts\Services\UserMissionServiceInterface
     */
    private UserMissionServiceInterface $userMissionService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->userService = app(UserServiceInterface::class);
        $this->walletService = app(WalletServiceInterface::class);
        $this->userMissionService = app(UserMissionServiceInterface::class);
    }

    /**
     * Handle the mission completion.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Mission $mission
     * @return void
     */
    public function handleMissionCompletion(User $user, Mission $mission): void
    {
        $this->awardCoinsAndExperience($user, $mission);

        $this->awardRewards($user, $mission);

        $this->userMissionService->markMissionComplete($user, $mission);
    }

    /**
     * Awaird mission coins and experience for user.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Mission $mission
     * @return void
     */
    public function awardCoinsAndExperience(User $user, Mission $mission): void
    {
        $this->walletService->addFunds($user, $mission->coins);
        $this->userService->addExperience($user->id, $mission->experience);
    }

    /**
     * Award the mission rewards.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Mission $mission
     * @return void
     */
    public function awardRewards(User $user, Mission $mission): void
    {
        // TODO: Implement the award rewards method when exists.
    }
}

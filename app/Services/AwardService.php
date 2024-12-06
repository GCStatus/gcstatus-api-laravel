<?php

namespace App\Services;

use Throwable;
use App\Models\{User, Mission, Rewardable};
use App\Contracts\Factories\RewardStrategyFactoryInterface;
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
     * The reward strategy.
     *
     * @var \App\Contracts\Factories\RewardStrategyFactoryInterface
     */
    private RewardStrategyFactoryInterface $rewardStrategyFactory;

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
        $this->rewardStrategyFactory = app(RewardStrategyFactoryInterface::class);
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
        if ($this->userMissionService->userAlreadyCompletedMission($user, $mission)) {
            return;
        }

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
        $mission->rewards->each(function (Rewardable $rewardable) use ($user) {
            try {
                $strategy = $this->rewardStrategyFactory->resolve($rewardable);

                $strategy->award($user, $rewardable);
            } catch (Throwable $e) {
                logService()->error(
                    'Failed to award title to user.',
                    $e->getMessage(),
                    $e->getTraceAsString(),
                );
            }
        });
    }
}

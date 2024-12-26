<?php

namespace App\Services;

use Throwable;
use App\Models\{User, Rewardable};
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Factories\RewardStrategyFactoryInterface;
use App\Contracts\Services\{
    UserServiceInterface,
    AwardServiceInterface,
    WalletServiceInterface,
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
        $this->rewardStrategyFactory = app(RewardStrategyFactoryInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function awardRewards(User $user, Collection $rewards): void
    {
        $rewards->each(function (Rewardable $rewardable) use ($user) {
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

    /**
     * @inheritDoc
     */
    public function awardExperience(User $user, int $amount): void
    {
        $this->userService->addExperience($user, $amount);
    }

    /**
     * @inheritDoc
     */
    public function awardCoins(User $user, int $amount, string $description): void
    {
        $this->walletService->addFunds($user, $amount, $description);
    }
}

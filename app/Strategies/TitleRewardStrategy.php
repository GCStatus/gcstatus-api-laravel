<?php

namespace App\Strategies;

use App\Models\{User, Title, Rewardable};
use App\Contracts\Services\UserTitleServiceInterface;
use App\Contracts\Strategies\RewardStrategyInterface;

class TitleRewardStrategy implements RewardStrategyInterface
{
    /**
     * The user title service.
     *
     * @var \App\Contracts\Services\UserTitleServiceInterface
     */
    private UserTitleServiceInterface $userTitleService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->userTitleService = app(UserTitleServiceInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function award(User $user, Rewardable $rewardable): void
    {
        if ($rewardable->rewardable instanceof Title) {
            /** @var \App\Models\Title $title */
            $title = $rewardable->rewardable;

            $this->userTitleService->assignTitleToUser($user, $title);
        }
    }
}

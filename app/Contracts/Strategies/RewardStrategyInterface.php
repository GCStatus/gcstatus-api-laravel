<?php

namespace App\Contracts\Strategies;

use App\Models\{User, Rewardable};

interface RewardStrategyInterface
{
    /**
     * Award a reward to a user.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Rewardable $rewardable
     * @return void
     */
    public function award(User $user, Rewardable $rewardable): void;
}

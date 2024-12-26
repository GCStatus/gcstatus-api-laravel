<?php

namespace App\Contracts\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface AwardServiceInterface
{
    /**
     * Award rewards for given user.
     *
     * @param \App\Models\User $user
     * @param \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rewardable> $rewards
     * @return void
     */
    public function awardRewards(User $user, Collection $rewards): void;

    /**
     * Award user with given experience.
     *
     * @param \App\Models\User $user
     * @param int $amount
     * @return void
     */
    public function awardExperience(User $user, int $amount): void;

    /**
     * Award user with given coins.
     *
     * @param \App\Models\User $user
     * @param int $amount
     * @param string $description
     * @return void
     */
    public function awardCoins(User $user, int $amount, string $description): void;
}

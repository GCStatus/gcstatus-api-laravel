<?php

namespace App\Contracts\Factories;

use App\Models\Rewardable;
use App\Contracts\Strategies\RewardStrategyInterface;

interface RewardStrategyFactoryInterface
{
    /**
     * Resolve a mission strategy for the given mission requirement.
     *
     * @param \App\Models\Rewardable $rewardable
     * @return \App\Contracts\Strategies\RewardStrategyInterface
     */
    public function resolve(Rewardable $rewardable): RewardStrategyInterface;

    /**
     * Register a strategy for a specific reward.
     *
     * @param string $rewardable_type
     * @param \App\Contracts\Strategies\RewardStrategyInterface $strategy
     */
    public function register(string $rewardable_type, RewardStrategyInterface $strategy): void;
}

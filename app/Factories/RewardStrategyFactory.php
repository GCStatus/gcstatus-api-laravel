<?php

namespace App\Factories;

use App\Models\Rewardable;
use InvalidArgumentException;
use App\Contracts\Strategies\RewardStrategyInterface;
use App\Contracts\Factories\RewardStrategyFactoryInterface;

class RewardStrategyFactory implements RewardStrategyFactoryInterface
{
    /**
     * The registry array.
     *
     * @var array<string, \App\Contracts\Strategies\RewardStrategyInterface>
     */
    private array $registry = [];

    /**
     * Register a reward strategy for a specific type.
     *
     * @param string $type
     * @param \App\Contracts\Strategies\RewardStrategyInterface $strategy
     * @return void
     */
    public function register(string $type, RewardStrategyInterface $strategy): void
    {
        $this->registry[$type] = $strategy;
    }

    /**
     * Get the reward strategy for the given rewardable type.
     *
     * @param \App\Models\Rewardable $rewardable
     * @return \App\Contracts\Strategies\RewardStrategyInterface
     */
    public function resolve(Rewardable $rewardable): RewardStrategyInterface
    {
        if (!isset($this->registry[$rewardable->rewardable_type])) {
            throw new InvalidArgumentException("No strategy found for reward: {$rewardable->rewardable_type}");
        }

        return $this->registry[$rewardable->rewardable_type];
    }
}

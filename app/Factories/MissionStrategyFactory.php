<?php

namespace App\Factories;

use InvalidArgumentException;
use App\Models\MissionRequirement;
use App\Contracts\Strategies\MissionStrategyInterface;
use App\Contracts\Factories\MissionStrategyFactoryInterface;

class MissionStrategyFactory implements MissionStrategyFactoryInterface
{
    /**
     * The registry array.
     *
     * @var array<string, \App\Contracts\Strategies\MissionStrategyInterface>
     */
    private array $registry = [];

    /**
     * Register a strategy for a specific key.
     *
     * @param string $key
     * @param \App\Contracts\Strategies\MissionStrategyInterface $strategy
     */
    public function register(string $key, MissionStrategyInterface $strategy): void
    {
        $this->registry[$key] = $strategy;
    }

    /**
     * Resolve a strategy for the given mission requirement.
     *
     * @param \App\Models\MissionRequirement $requirement
     * @return \App\Contracts\Strategies\MissionStrategyInterface
     * @throws InvalidArgumentException
     */
    public function resolve(MissionRequirement $requirement): MissionStrategyInterface
    {
        if (!isset($this->registry[$requirement->key])) {
            throw new InvalidArgumentException("No strategy found for key: {$requirement->key}");
        }

        return $this->registry[$requirement->key];
    }
}

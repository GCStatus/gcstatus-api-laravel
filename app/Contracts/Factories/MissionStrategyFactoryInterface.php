<?php

namespace App\Contracts\Factories;

use App\Models\MissionRequirement;
use App\Contracts\Strategies\MissionStrategyInterface;

interface MissionStrategyFactoryInterface
{
    /**
     * Resolve a mission strategy for the given mission requirement.
     *
     * @param \App\Models\MissionRequirement $requirement
     * @return \App\Contracts\Strategies\MissionStrategyInterface
     */
    public function resolve(MissionRequirement $requirement): MissionStrategyInterface;

    /**
     * Register a strategy for a specific key.
     *
     * @param string $key
     * @param \App\Contracts\Strategies\MissionStrategyInterface $strategy
     */
    public function register(string $key, MissionStrategyInterface $strategy): void;
}

<?php

namespace App\Services;

use App\Models\{User, Mission, MissionRequirement};
use App\Contracts\Factories\MissionStrategyFactoryInterface;
use App\Contracts\Services\ProgressCalculatorServiceInterface;

class ProgressCalculatorService implements ProgressCalculatorServiceInterface
{
    /**
     * The mission strategy factory.
     *
     * @var \App\Contracts\Factories\MissionStrategyFactoryInterface
     */
    private MissionStrategyFactoryInterface $factory;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Factories\MissionStrategyFactoryInterface $factory
     * @return void
     */
    public function __construct(MissionStrategyFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Determine the progress for a given mission requirement.
     *
     * @param \App\Models\User $user
     * @param \App\Models\MissionRequirement $requirement
     * @return int
     */
    public function determineProgress(User $user, MissionRequirement $requirement): int
    {
        $strategy = $this->factory->resolve($requirement);

        return $strategy->calculateProgress($user, $requirement);
    }

    /**
     * Check if requirement is complete.
     *
     * @param \App\Models\User $user
     * @param \App\Models\MissionRequirement $requirement
     * @return bool
     */
    public function isRequirementComplete(User $user, MissionRequirement $requirement): bool
    {
        $progress = $this->determineProgress($user, $requirement);

        return $progress >= $requirement->goal;
    }

    /**
     * Check if mission is complete.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Mission $mission
     * @return bool
     */
    public function isMissionComplete(User $user, Mission $mission): bool
    {
        foreach ($mission->requirements as $requirement) {
            if (!$this->isRequirementComplete($user, $requirement)) {
                return false;
            }
        }

        return true;
    }
}

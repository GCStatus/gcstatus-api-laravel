<?php

namespace App\Services;

use App\Models\{User, Mission, MissionRequirement, Status};
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
        /** @var \App\Models\Mission $mission */
        $mission = $requirement->mission;

        if ($this->assertCanCalculate($mission)) {
            $strategy = $this->factory->resolve($requirement);

            return $strategy->calculateProgress($user, $requirement);
        }

        return 0;
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
        $mission->load('requirements');

        foreach ($mission->requirements as $requirement) {
            if (!$this->isRequirementComplete($user, $requirement)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Assert can calculate the mission progress.
     *
     * @param \App\Models\Mission $mission
     * @return bool
     */
    private function assertCanCalculate(Mission $mission): bool
    {
        return (int)$mission->status_id === Status::AVAILABLE_STATUS_ID;
    }
}

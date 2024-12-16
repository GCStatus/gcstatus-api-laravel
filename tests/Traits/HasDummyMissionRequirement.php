<?php

namespace Tests\Traits;

use App\Models\{Mission, MissionRequirement};

trait HasDummyMissionRequirement
{
    /**
     * Create a dummy mission requirement.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\MissionRequirement
     */
    public function createDummyMissionRequirement(array $data = []): MissionRequirement
    {
        return MissionRequirement::factory()->create($data);
    }

    /**
     * Create a dummy mission requirement to mission.
     *
     * @param \App\Models\Mission $mission
     * @param array<string, mixed> $data
     * @return \App\Models\MissionRequirement
     */
    public function createDummyMissionRequirementTo(Mission $mission, array $data = []): MissionRequirement
    {
        $missionRequirement = $this->createDummyMissionRequirement($data);

        $missionRequirement->mission()->delete();

        $missionRequirement->mission()->associate($mission)->save();

        return $missionRequirement;
    }
}

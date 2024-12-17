<?php

namespace Tests\Feature\Http\Mission;

use App\Models\{User, Mission};
use Tests\Feature\Http\BaseIntegrationTesting;
use Tests\Traits\{HasDummyMission, HasDummyMissionRequirement};

class MissionCompleteTest extends BaseIntegrationTesting
{
    use HasDummyMission;
    use HasDummyMissionRequirement;

    /**
     * The dummy user.
     *
     * @var \App\Models\User
     */
    private User $user;

    /**
     * The dummy mission.
     *
     * @var \App\Models\Mission
     */
    private Mission $mission;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = $this->actingAsDummyUser();
        $this->mission = $this->createDummyMission([
            'for_all' => true,
        ]);

        $this->createDummyMissionRequirementTo($this->mission);
    }
}

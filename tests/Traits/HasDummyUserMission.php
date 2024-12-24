<?php

namespace Tests\Traits;

use App\Models\UserMission;

trait HasDummyUserMission
{
    /**
     * Create a dummy user mission.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\UserMission
     */
    public function createDummyUserMission(array $data = []): UserMission
    {
        return UserMission::factory()->create($data);
    }
}

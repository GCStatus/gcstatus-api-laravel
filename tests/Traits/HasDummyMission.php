<?php

namespace Tests\Traits;

use App\Models\Mission;

trait HasDummyMission
{
    /**
     * Create dummy mission.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Mission
     */
    public function createDummyMission(array $data = []): Mission
    {
        return Mission::factory()->create($data);
    }
}

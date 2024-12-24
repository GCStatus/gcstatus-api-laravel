<?php

namespace Tests\Traits;

use App\Models\Rewardable;

trait HasDummyRewardable
{
    /**
     * Create a dummy rewardable.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Rewardable
     */
    public function createDummyRewardable(array $data = []): Rewardable
    {
        return Rewardable::factory()->create($data);
    }
}

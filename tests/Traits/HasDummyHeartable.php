<?php

namespace Tests\Traits;

use App\Models\Heartable;

trait HasDummyHeartable
{
    /**
     * Create a dummy heartable.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Heartable
     */
    public function createDummyHeartable(array $data = []): Heartable
    {
        return Heartable::factory()->create($data);
    }
}

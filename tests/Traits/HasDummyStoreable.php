<?php

namespace Tests\Traits;

use App\Models\Storeable;

trait HasDummyStoreable
{
    /**
     * Create a dummy storeable.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Storeable
     */
    public function createDummyStoreable(array $data = []): Storeable
    {
        return Storeable::factory()->create($data);
    }
}

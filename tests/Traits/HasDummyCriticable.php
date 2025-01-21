<?php

namespace Tests\Traits;

use App\Models\Criticable;

trait HasDummyCriticable
{
    /**
     * Create a dummy criticable.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Criticable
     */
    public function createDummyCriticable(array $data = []): Criticable
    {
        return Criticable::factory()->create($data);
    }
}

<?php

namespace Tests\Traits;

use App\Models\Status;

trait HasDummyStatus
{
    /**
     * Create a dummy status.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Status
     */
    public function createDummyStatus(array $data = []): Status
    {
        return Status::factory()->create($data);
    }
}

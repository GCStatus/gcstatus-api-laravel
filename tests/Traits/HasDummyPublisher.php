<?php

namespace Tests\Traits;

use App\Models\Publisher;

trait HasDummyPublisher
{
    /**
     * Create a dummy publisher.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Publisher
     */
    public function createDummyPublisher(array $data = []): Publisher
    {
        return Publisher::factory()->create($data);
    }
}

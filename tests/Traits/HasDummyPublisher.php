<?php

namespace Tests\Traits;

use App\Models\Publisher;
use Illuminate\Database\Eloquent\Collection;

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

    /**
     * Create dummy publishers.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Publisher>
     */
    public function createDummyPublishers(int $times, array $data = []): Collection
    {
        return Publisher::factory($times)->create($data);
    }
}

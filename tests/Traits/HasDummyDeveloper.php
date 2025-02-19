<?php

namespace Tests\Traits;

use App\Models\Developer;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyDeveloper
{
    /**
     * Create a dummy developer.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Developer
     */
    public function createDummyDeveloper(array $data = []): Developer
    {
        return Developer::factory()->create($data);
    }

    /**
     * Create dummy developers.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Developer>
     */
    public function createDummyDevelopers(int $times, array $data = []): Collection
    {
        return Developer::factory($times)->create($data);
    }
}

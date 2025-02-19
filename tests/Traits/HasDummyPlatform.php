<?php

namespace Tests\Traits;

use App\Models\Platform;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyPlatform
{
    /**
     * Create a dummy platform.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Platform
     */
    public function createDummyPlatform(array $data = []): Platform
    {
        return Platform::factory()->create($data);
    }

    /**
     * Create dummy platforms.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Platform>
     */
    public function createDummyPlatforms(int $times, array $data = []): Collection
    {
        return Platform::factory($times)->create($data);
    }
}

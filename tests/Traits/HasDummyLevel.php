<?php

namespace Tests\Traits;

use App\Models\Level;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyLevel
{
    /**
     * Create dummy level.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Level
     */
    public function createDummyLevel(array $data = []): Level
    {
        /** @var \App\Models\Level $level */
        $level = Level::factory()->create($data);

        return $level;
    }

    /**
     * Create dummy levels.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, Level>
     */
    public function createDummyLevels(int $times, array $data = []): Collection
    {
        return Level::factory($times)->create($data);
    }
}

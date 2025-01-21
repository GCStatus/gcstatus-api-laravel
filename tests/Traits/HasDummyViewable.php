<?php

namespace Tests\Traits;

use App\Models\Viewable;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyViewable
{
    /**
     * Create a dummy viewable.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Viewable
     */
    public function createDummyViewable(array $data = []): Viewable
    {
        return Viewable::factory()->create($data);
    }

    /**
     * Create dummy viewables.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Viewable>
     */
    public function createDummyViewables(int $times, array $data = []): Collection
    {
        return Viewable::factory($times)->create($data);
    }
}

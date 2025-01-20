<?php

namespace Tests\Traits;

use App\Models\Galleriable;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyGalleriable
{
    /**
     * Create a dummy galleriable.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Galleriable
     */
    public function createDummyGalleriable(array $data = []): Galleriable
    {
        return Galleriable::factory()->create($data);
    }

    /**
     * Create dummy galleriables.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Galleriable>
     */
    public function createDummyGalleriables(int $times, array $data = []): Collection
    {
        return Galleriable::factory($times)->create($data);
    }
}

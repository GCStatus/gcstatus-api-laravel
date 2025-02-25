<?php

namespace Tests\Traits;

use App\Models\Cracker;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyCracker
{
    /**
     * Create a dummy Cracker.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Cracker
     */
    public function createDummyCracker(array $data = []): Cracker
    {
        return Cracker::factory()->create($data);
    }

    /**
     * Create dummy crackers.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Cracker>
     */
    public function createDummyCrackers(int $times, array $data = []): Collection
    {
        return Cracker::factory($times)->create($data);
    }
}

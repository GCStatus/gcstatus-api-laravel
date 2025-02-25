<?php

namespace Tests\Traits;

use App\Models\Protection;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyProtection
{
    /**
     * Create a dummy Protection.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Protection
     */
    public function createDummyProtection(array $data = []): Protection
    {
        return Protection::factory()->create($data);
    }

    /**
     * Create dummy protections.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Protection>
     */
    public function createDummyProtections(int $times, array $data = []): Collection
    {
        return Protection::factory($times)->create($data);
    }
}

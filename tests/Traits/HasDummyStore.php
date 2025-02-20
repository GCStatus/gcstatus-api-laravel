<?php

namespace Tests\Traits;

use App\Models\Store;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyStore
{
    /**
     * Create a dummy store.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Store
     */
    public function createDummyStore(array $data = []): Store
    {
        return Store::factory()->create($data);
    }

    /**
     * Create dummy stores.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Store>
     */
    public function createDummyStores(int $times, array $data = []): Collection
    {
        return Store::factory($times)->create($data);
    }
}

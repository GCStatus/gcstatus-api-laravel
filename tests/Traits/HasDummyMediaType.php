<?php

namespace Tests\Traits;

use App\Models\MediaType;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyMediaType
{
    /**
     * Create dummy media type.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\MediaType
     */
    public function createDummyMediaType(array $data = []): MediaType
    {
        return MediaType::factory()->create($data);
    }

    /**
     * Create dummy media types.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\MediaType>
     */
    public function createDummyMediaTypes(int $times, array $data = []): Collection
    {
        return MediaType::factory($times)->create($data);
    }
}

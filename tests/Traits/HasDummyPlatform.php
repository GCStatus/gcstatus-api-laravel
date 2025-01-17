<?php

namespace Tests\Traits;

use App\Models\Platform;

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
}

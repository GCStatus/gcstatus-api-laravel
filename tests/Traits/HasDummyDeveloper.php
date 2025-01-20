<?php

namespace Tests\Traits;

use App\Models\Developer;

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
}

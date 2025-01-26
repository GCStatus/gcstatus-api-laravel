<?php

namespace Tests\Traits;

use App\Models\Protection;

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
}

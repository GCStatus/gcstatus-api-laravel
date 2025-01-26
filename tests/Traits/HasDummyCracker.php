<?php

namespace Tests\Traits;

use App\Models\Cracker;

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
}

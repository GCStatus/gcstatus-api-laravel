<?php

namespace Tests\Traits;

use App\Models\Languageable;

trait HasDummyLanguageable
{
    /**
     * Create a dummy languageable.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Languageable
     */
    public function createDummyLanguageable(array $data = []): Languageable
    {
        return Languageable::factory()->create($data);
    }
}

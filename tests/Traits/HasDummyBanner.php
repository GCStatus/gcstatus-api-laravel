<?php

namespace Tests\Traits;

use App\Models\Banner;

trait HasDummyBanner
{
    /**
     * Create dummy banner.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Banner
     */
    public function createDummyBanner(array $data = []): Banner
    {
        return Banner::factory()->create($data);
    }
}

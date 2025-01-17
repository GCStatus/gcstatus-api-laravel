<?php

namespace Tests\Traits;

use App\Models\Tag;

trait HasDummyTag
{
    /**
     * Create a dummy tag.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Tag
     */
    public function createDummyTag(array $data = []): Tag
    {
        return Tag::factory()->create($data);
    }
}

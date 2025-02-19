<?php

namespace Tests\Traits;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

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

    /**
     * Create dummy tags.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag>
     */
    public function createDummyTags(int $times, array $data = []): Collection
    {
        return Tag::factory($times)->create($data);
    }
}

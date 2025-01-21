<?php

namespace Tests\Traits;

use App\Models\Commentable;

trait HasDummyCommentable
{
    /**
     * Create a dummy commentable.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Commentable
     */
    public function createDummyCommentable(array $data = []): Commentable
    {
        return Commentable::factory()->create($data);
    }
}

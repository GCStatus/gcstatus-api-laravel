<?php

namespace Tests\Traits;

use App\Models\Reviewable;

trait HasDummyReviewable
{
    /**
     * Create a dummy reviewable.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Reviewable
     */
    public function createDummyReviewable(array $data = []): Reviewable
    {
        return Reviewable::factory()->create($data);
    }
}

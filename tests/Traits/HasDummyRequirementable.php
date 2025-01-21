<?php

namespace Tests\Traits;

use App\Models\Requirementable;

trait HasDummyRequirementable
{
    /**
     * Create a dummy requirementable.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Requirementable
     */
    public function createDummyRequirementable(array $data = []): Requirementable
    {
        return Requirementable::factory()->create($data);
    }
}

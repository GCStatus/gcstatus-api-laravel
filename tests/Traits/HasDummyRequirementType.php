<?php

namespace Tests\Traits;

use App\Models\RequirementType;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyRequirementType
{
    /**
     * Create a dummy requirement type.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\RequirementType
     */
    public function createDummyRequirementType(array $data = []): RequirementType
    {
        return RequirementType::factory()->create($data);
    }

    /**
     * Create dummy requirement types.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\RequirementType>
     */
    public function createDummyRequirementTypes(int $times, array $data = []): Collection
    {
        return RequirementType::factory($times)->create($data);
    }
}

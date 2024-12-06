<?php

namespace App\Repositories;

use App\Models\{Status, MissionRequirement};
use Illuminate\Database\Eloquent\{Builder, Collection};
use App\Contracts\Repositories\MissionRequirementRepositoryInterface;

class MissionRequirementRepository implements MissionRequirementRepositoryInterface
{
    /**
     * Get all requirements by key.
     *
     * @param string $key
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\MissionRequirement>
     */
    public function findByKey(string $key): Collection
    {
        return MissionRequirement::query()
            ->where('key', $key)
            ->whereHas('mission', function (Builder $query) {
                $query->where('status_id', Status::AVAILABLE_STATUS_ID);
            })->get();
    }
}

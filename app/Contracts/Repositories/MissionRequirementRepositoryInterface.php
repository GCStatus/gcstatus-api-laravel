<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface MissionRequirementRepositoryInterface
{
    /**
     * Get all requirements by key.
     *
     * @param string $key
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\MissionRequirement>
     */
    public function findByKey(string $key): Collection;
}

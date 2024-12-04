<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;

interface MissionRequirementServiceInterface
{
    /**
     * Get all requirements by key.
     *
     * @param string $key
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\MissionRequirement>
     */
    public function findByKey(string $key): Collection;
}

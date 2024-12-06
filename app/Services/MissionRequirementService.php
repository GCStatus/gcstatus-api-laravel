<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Services\MissionRequirementServiceInterface;
use App\Contracts\Repositories\MissionRequirementRepositoryInterface;

class MissionRequirementService implements MissionRequirementServiceInterface
{
    /**
     * The mission requirement repository.
     *
     * @var \App\Contracts\Repositories\MissionRequirementRepositoryInterface
     */
    private MissionRequirementRepositoryInterface $missionRequirementRepository;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->missionRequirementRepository = app(MissionRequirementRepositoryInterface::class);
    }

    /**
     * Get all requirements by key.
     *
     * @param string $key
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\MissionRequirement>
     */
    public function findByKey(string $key): Collection
    {
        return $this->missionRequirementRepository->findByKey($key);
    }
}

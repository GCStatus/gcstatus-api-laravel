<?php

namespace App\Repositories;

use App\Models\Developer;
use App\Contracts\Repositories\DeveloperRepositoryInterface;

class DeveloperRepository extends AbstractRepository implements DeveloperRepositoryInterface
{
    /**
     * The developer model.
     *
     * @return \App\Models\Developer
     */
    public function model(): Developer
    {
        return new Developer();
    }

    /**
     * @inheritDoc
     */
    public function existsByName(string $name): bool
    {
        return $this->model()
            ->query()
            ->where('name', $name)
            ->exists();
    }
}

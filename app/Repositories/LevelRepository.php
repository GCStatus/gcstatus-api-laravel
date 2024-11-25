<?php

namespace App\Repositories;

use App\Models\Level;
use App\Contracts\Repositories\LevelRepositoryInterface;

class LevelRepository extends AbstractRepository implements LevelRepositoryInterface
{
    /**
     * The level model.
     *
     * @return \App\Models\Level
     */
    public function model(): Level
    {
        return new Level();
    }
}

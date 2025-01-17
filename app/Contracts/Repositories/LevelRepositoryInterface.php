<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface LevelRepositoryInterface extends AbstractRepositoryInterface
{
    /**
     * Get all levels.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model>
     */
    public function all(): Collection;

    /**
     * Get all levels higher than the given level, ordered by level.
     *
     * @param int $currentLevel
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Level>
     */
    public function getLevelsAboveByLevel(int $currentLevel): Collection;
}

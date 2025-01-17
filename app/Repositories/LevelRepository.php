<?php

namespace App\Repositories;

use App\Models\Level;
use Illuminate\Database\Eloquent\Collection;
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

    /**
     * @inheritDoc
     */
    public function all(): Collection
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Database\Eloquent\Model> $levels */
        $levels = $this->model()
            ->query()
            ->with('rewards.rewardable', 'rewards.sourceable')
            ->orderByDesc('created_at')
            ->get();

        return $levels;
    }

    /**
     * @inheritDoc
     */
    public function getLevelsAboveByLevel(int $currentLevel): Collection
    {
        return $this->model()
            ->query()
            ->where('level', '>', $currentLevel)
            ->orderBy('level', 'asc')
            ->get();
    }
}

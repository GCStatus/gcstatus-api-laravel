<?php

namespace App\Repositories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Repositories\GameRepositoryInterface;

class GameRepository extends AbstractRepository implements GameRepositoryInterface
{
    /**
     * The game model.
     *
     * @return \App\Models\Game
     */
    public function model(): Game
    {
        return new Game();
    }

    /**
     * @inheritDoc
     */
    public function getGamesByCondition(string $condition, int $limit = 100): Collection
    {
        return $this->model()
            ->query()
            ->where('condition', $condition)
            ->withIsHearted()
            ->limit($limit)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getUpcomingGames(int $limit = 100): Collection
    {
        return $this->model()
            ->query()
            ->where('release_date', '>', today())
            ->withIsHearted()
            ->limit($limit)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getMostLikedGames(int $limit = 100): Collection
    {
        return $this->model()
            ->query()
            ->orderByDesc('hearts_count')
            ->withIsHearted()
            ->limit($limit)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getNextGreatRelease(): ?Game
    {
        return $this->model()
            ->query()
            ->where('great_release', true)
            ->where('release_date', '>=', today())
            ->first();
    }
}

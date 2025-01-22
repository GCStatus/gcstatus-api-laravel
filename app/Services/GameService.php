<?php

namespace App\Services;

use App\Models\Game;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\Services\GameServiceInterface;
use App\Contracts\Repositories\GameRepositoryInterface;

class GameService extends AbstractService implements GameServiceInterface
{
    /**
     * The game repository.
     *
     * @return \App\Contracts\Repositories\GameRepositoryInterface
     */
    public function repository(): GameRepositoryInterface
    {
        return app(GameRepositoryInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function getCalendarGames(): Collection
    {
        return $this->repository()->getCalendarGames();
    }

    /**
     * @inheritDoc
     */
    public function details(string $slug): Game
    {
        $game = $this->repository()->details($slug);

        $this->createGameView($game);

        return $game;
    }

    /**
     * @inheritDoc
     */
    public function getGamesByCondition(string $condition, int $limit): Collection
    {
        return $this->repository()->getGamesByCondition($condition, $limit);
    }

    /**
     * @inheritDoc
     */
    public function getUpcomingGames(int $limit): Collection
    {
        return $this->repository()->getUpcomingGames($limit);
    }

    /**
     * @inheritDoc
     */
    public function getMostLikedGames(int $limit): Collection
    {
        return $this->repository()->getMostLikedGames($limit);
    }

    /**
     * @inheritDoc
     */
    public function getNextGreatRelease(): ?Game
    {
        return $this->repository()->getNextGreatRelease();
    }

    /**
     * Create the related game view.
     *
     * @param \App\Models\Game $game
     * @return void
     */
    private function createGameView(Game $game): void
    {
        $game->increment('views');
    }
}

<?php

namespace App\Contracts\Services;

use App\Models\Game;
use Illuminate\Database\Eloquent\Collection;

interface GameServiceInterface extends AbstractServiceInterface
{
    /**
     * Get games by condition.
     *
     * @param string $condition
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Game>
     */
    public function getGamesByCondition(string $condition, int $limit): Collection;

    /**
     * Get the upcoming games.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Game>
     */
    public function getUpcomingGames(int $limit): Collection;

    /**
     * Get the most liked games.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Game>
     */
    public function getMostLikedGames(int $limit): Collection;

    /**
     * Get the next release game.
     *
     * @return ?\App\Models\Game
     */
    public function getNextGreatRelease(): ?Game;
}

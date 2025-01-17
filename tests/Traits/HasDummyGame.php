<?php

namespace Tests\Traits;

use App\Models\Game;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyGame
{
    /**
     * Create a dummy game.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Game
     */
    public function createDummyGame(array $data = []): Game
    {
        return Game::factory()->create($data);
    }

    /**
     * Create dummy games.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Game>
     */
    public function createDummyGames(int $times, array $data = []): Collection
    {
        return Game::factory($times)->create($data);
    }
}

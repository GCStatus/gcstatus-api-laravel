<?php

namespace Tests\Traits;

use App\Models\{Game, GameSupport};

trait HasDummyGameSupport
{
    /**
     * Create a dummy game support.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\GameSupport
     */
    public function createDummyGameSupport(array $data = []): GameSupport
    {
        return GameSupport::factory()->create($data);
    }

    /**
     * Create a dummy game support to a game.
     *
     * @param \App\Models\Game $game
     * @param array<string, mixed> $data
     * @return \App\Models\GameSupport
     */
    public function createDummyGameSupportTo(Game $game, array $data = []): GameSupport
    {
        return $this->createDummyGameSupport([
            'game_id' => $game->id,
        ], ...$data);
    }
}

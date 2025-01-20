<?php

namespace Tests\Traits;

use App\Models\{Dlc, Game};

trait HasDummyDlc
{
    /**
     * Create a dummy dlc.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Dlc
     */
    public function createDummyDlc(array $data = []): Dlc
    {
        return Dlc::factory()->create($data);
    }

    /**
     * Create a dummy dlc to a game.
     *
     * @param \App\Models\Game $game
     * @param array<string, mixed> $data
     * @return \App\Models\Dlc
     */
    public function createDummyDlcTo(Game $game, array $data = []): Dlc
    {
        return $this->createDummyDlc([
            'game_id' => $game->id,
        ], ...$data);
    }
}

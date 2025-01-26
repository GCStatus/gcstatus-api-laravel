<?php

namespace Tests\Traits;

use App\Models\{Crack, Game};

trait HasDummyCrack
{
    /**
     * Create a dummy crack.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Crack
     */
    public function createDummyCrack(array $data = []): Crack
    {
        return Crack::factory()->create($data);
    }

    /**
     * Create a dummy crack to a game.
     *
     * @param \App\Models\Game $game
     * @param array<string, mixed> $data
     * @return \App\Models\Crack
     */
    public function createDummyCrackTo(Game $game, array $data = []): Crack
    {
        return $this->createDummyCrack(array_merge([
            'game_id' => $game->id,
        ], $data));
    }
}

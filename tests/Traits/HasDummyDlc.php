<?php

namespace Tests\Traits;

use App\Models\{Dlc, Game};
use Illuminate\Database\Eloquent\Collection;

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
     * Create dummy dlcs.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Dlc>
     */
    public function createDummyDlcs(int $times, array $data = []): Collection
    {
        return Dlc::factory($times)->create($data);
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

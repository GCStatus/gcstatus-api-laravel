<?php

namespace Tests\Traits;

use App\Models\{Torrent, Game};

trait HasDummyTorrent
{
    /**
     * Create a dummy torrent.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Torrent
     */
    public function createDummyTorrent(array $data = []): Torrent
    {
        return Torrent::factory()->create($data);
    }

    /**
     * Create a dummy torrent to a game.
     *
     * @param \App\Models\Game $game
     * @param array<string, mixed> $data
     * @return \App\Models\Torrent
     */
    public function createDummyTorrentTo(Game $game, array $data = []): Torrent
    {
        return $this->createDummyTorrent([
            'game_id' => $game->id,
        ], ...$data);
    }
}

<?php

namespace Tests\Traits;

use App\Models\TorrentProvider;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyTorrentProvider
{
    /**
     * Create a dummy torrent provider.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\TorrentProvider
     */
    public function createDummyTorrentProvider(array $data = []): TorrentProvider
    {
        return TorrentProvider::factory()->create($data);
    }

    /**
     * Create dummy torrent providers.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\TorrentProvider>
     */
    public function createDummyTorrentProviders(int $times, array $data = []): Collection
    {
        return TorrentProvider::factory($times)->create($data);
    }
}

<?php

namespace Tests\Traits;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Collection;

trait HasDummyGenre
{
    /**
     * Create a dummy genre.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Genre
     */
    public function createDummyGenre(array $data = []): Genre
    {
        return Genre::factory()->create($data);
    }

    /**
     * Create dummy genres.
     *
     * @param int $times
     * @param array<string, mixed> $data
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Genre>
     */
    public function createDummyGenres(int $times, array $data = []): Collection
    {
        return Genre::factory($times)->create($data);
    }
}

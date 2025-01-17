<?php

namespace Tests\Traits;

use App\Models\Genre;

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
}

<?php

namespace Tests\Traits;

use App\Models\Friendship;

trait HasDummyFriendship
{
    /**
     * Create a dummy friendship.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\Friendship
     */
    public function createDummyFriendship(array $data = []): Friendship
    {
        return Friendship::factory()->create($data);
    }
}

<?php

namespace Tests\Traits;

use App\Models\FriendRequest;

trait HasDummyFriendRequest
{
    /**
     * Create a dummy friend request.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\FriendRequest
     */
    public function createDummyFriendRequest(array $data = []): FriendRequest
    {
        return FriendRequest::factory()->create($data);
    }
}

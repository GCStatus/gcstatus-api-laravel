<?php

namespace App\Repositories;

use App\Models\Friendship;
use App\Contracts\Repositories\FriendshipRepositoryInterface;

class FriendshipRepository extends AbstractRepository implements FriendshipRepositoryInterface
{
    /**
     * The friendship model.
     *
     * @return \App\Models\Friendship
     */
    public function model(): Friendship
    {
        return new Friendship();
    }
}

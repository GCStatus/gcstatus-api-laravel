<?php

namespace App\Services;

use App\Contracts\Services\FriendshipServiceInterface;
use App\Contracts\Repositories\FriendshipRepositoryInterface;

class FriendshipService extends AbstractService implements FriendshipServiceInterface
{
    /**
     * The friendship repository.
     *
     * @return \App\Contracts\Repositories\FriendshipRepositoryInterface
     */
    public function repository(): FriendshipRepositoryInterface
    {
        return app(FriendshipRepositoryInterface::class);
    }
}

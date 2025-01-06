<?php

namespace App\Contracts\Repositories;

interface FriendshipRepositoryInterface extends AbstractRepositoryInterface
{
    /**
     * Check friendship exists.
     *
     * @param mixed $userId
     * @param mixed $friendId
     * @return bool
     */
    public function friendshipExists(mixed $userId, mixed $friendId): bool;
}

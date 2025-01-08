<?php

namespace App\Contracts\Services;

interface FriendshipServiceInterface extends AbstractServiceInterface
{
    /**
     * Check if friendship already exists.
     *
     * @param mixed $userId
     * @param mixed $friendId
     * @return bool
     */
    public function exists(mixed $userId, mixed $friendId): bool;
}

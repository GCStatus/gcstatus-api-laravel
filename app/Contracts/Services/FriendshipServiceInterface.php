<?php

namespace App\Contracts\Services;

interface FriendshipServiceInterface extends AbstractServiceInterface
{
    /**
     * Check if friendship already exists.
     *
     * @param mixed $friendId
     * @return bool
     */
    public function exists(mixed $friendId): bool;
}

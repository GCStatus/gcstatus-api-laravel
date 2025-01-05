<?php

namespace App\Contracts\Repositories;

interface FriendRequestRepositoryInterface extends AbstractRepositoryInterface
{
    /**
     * Check if friend requesst already exists.
     *
     * @param mixed $userId
     * @param mixed $addresseeId
     * @return bool
     */
    public function exists(mixed $userId, mixed $addresseeId): bool;
}

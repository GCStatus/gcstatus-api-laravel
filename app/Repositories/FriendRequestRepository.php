<?php

namespace App\Repositories;

use App\Models\FriendRequest;
use App\Contracts\Repositories\FriendRequestRepositoryInterface;

class FriendRequestRepository extends AbstractRepository implements FriendRequestRepositoryInterface
{
    /**
     * The friend request model.
     *
     * @return \App\Models\FriendRequest
     */
    public function model(): FriendRequest
    {
        return new FriendRequest();
    }

    /**
     * Check if friend requesst already exists.
     *
     * @param mixed $userId
     * @param mixed $addresseeId
     * @return bool
     */
    public function exists(mixed $userId, mixed $addresseeId): bool
    {
        return $this->model()
            ->query()
            ->where('requester_id', $userId)
            ->where('addressee_id', $addresseeId)
            ->exists();
    }
}

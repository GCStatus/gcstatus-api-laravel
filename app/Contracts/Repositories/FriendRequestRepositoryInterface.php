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

    /**
     * Check if a reciprocal friend request exists.
     *
     * @param mixed $userId
     * @param mixed $addresseeId
     * @return bool
     */
    public function reciprocalRequestExists(mixed $userId, mixed $addresseeId): bool;

    /**
     * Delete reciprocal requests.
     *
     * @param mixed $userId
     * @param mixed $friendId
     * @return void
     */
    public function deleteReciprocalRequests(mixed $userId, mixed $friendId): void;
}

<?php

namespace App\Contracts\Services;

interface FriendRequestServiceInterface extends AbstractServiceInterface
{
    /**
     * Send a friend request to an user.
     *
     * @param mixed $addresseeId
     * @return void
     */
    public function send(mixed $addresseeId): void;

    /**
     * Accept a friend request.
     *
     * @param mixed $id
     * @return void
     */
    public function accept(mixed $id): void;

    /**
     * Decline a friend request.
     *
     * @param mixed $id
     * @return void
     */
    public function decline(mixed $id): void;
}

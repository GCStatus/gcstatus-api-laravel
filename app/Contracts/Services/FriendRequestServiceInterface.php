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
}

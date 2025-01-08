<?php

namespace App\Contracts\Services;

use App\Models\FriendRequest;

interface FriendRequestNotificationServiceInterface
{
    /**
     * Notify an addressee about a friend request.
     *
     * @param \App\Models\FriendRequest $friendRequest
     * @return void
     */
    public function notifyNewFriendRequest(FriendRequest $friendRequest): void;
}

<?php

namespace App\Contracts\Services;

use App\Models\Friendship;

interface FriendshipNotificationServiceInterface
{
    /**
     * Notify about a friend request acceptance.
     *
     * @param \App\Models\Friendship $friendship
     * @return void
     */
    public function notifyNewFriendship(Friendship $friendship): void;
}

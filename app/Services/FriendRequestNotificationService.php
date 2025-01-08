<?php

namespace App\Services;

use App\Models\FriendRequest;
use App\Notifications\DatabaseNotification;
use App\Contracts\Services\FriendRequestNotificationServiceInterface;

class FriendRequestNotificationService implements FriendRequestNotificationServiceInterface
{
    /**
     * Notify an addressee about a friend request.
     *
     * @param \App\Models\FriendRequest $friendRequest
     * @return void
     */
    public function notifyNewFriendRequest(FriendRequest $friendRequest): void
    {
        /** @var \App\Models\User $notifiable */
        $notifiable = $friendRequest->addressee;

        $notification = [
            'userId' => (string)$notifiable->id,
            'icon' => 'FaUserFriends',
            'title' => 'You have a new friend request.',
            'actionUrl' => "/dummy-route",
        ];

        $notifiable->notify(new DatabaseNotification($notification));
    }
}

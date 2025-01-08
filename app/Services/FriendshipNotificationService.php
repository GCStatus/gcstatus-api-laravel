<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\{User, Friendship};
use App\Notifications\DatabaseNotification;
use App\Contracts\Services\FriendshipNotificationServiceInterface;

class FriendshipNotificationService implements FriendshipNotificationServiceInterface
{
    /**
     * Notify about a friend request acceptance.
     *
     * @param \App\Models\Friendship $friendship
     * @return void
     */
    public function notifyNewFriendship(Friendship $friendship): void
    {
        /** @var \App\Models\User $user */
        $user = $friendship->user;

        /** @var \App\Models\User $friend */
        $friend = $friendship->friend;

        $this->notifyFriendship($user, $friend);
    }

    /**
     * Notify the user/friend about new friendship.
     *
     * @param \App\Models\User $notifiable
     * @param \App\Models\User $friend
     * @return void
     */
    private function notifyFriendship(User $notifiable, User $friend): void
    {
        $friendName = Str::before($friend->name, ' ');

        $notification = [
            'userId' => (string)$notifiable->id,
            'icon' => 'FaUserFriends',
            'title' => "You and $friendName are now friends!",
            'actionUrl' => "/dummy-route",
        ];

        $notifiable->notify(new DatabaseNotification($notification));
    }
}

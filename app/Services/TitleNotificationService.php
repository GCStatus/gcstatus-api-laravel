<?php

namespace App\Services;

use App\Models\{Title, User};
use App\Notifications\DatabaseNotification;
use App\Contracts\Services\TitleNotificationServiceInterface;

class TitleNotificationService implements TitleNotificationServiceInterface
{
    /**
     * @inheritDoc
     */
    public function notifyNewTitle(User $user, Title $title): void
    {
        $notification = [
            'userId' => (string)$user->id,
            'icon' => 'FaMedal',
            'title' => 'You earned a new title!',
            'actionUrl' => "/profile/?section=titles&id={$title->id}",
        ];

        $user->notify(new DatabaseNotification($notification));
    }
}

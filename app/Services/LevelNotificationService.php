<?php

namespace App\Services;

use App\Models\{User, Level};
use App\Notifications\DatabaseNotification;
use App\Contracts\Services\LevelNotificationServiceInterface;

class LevelNotificationService implements LevelNotificationServiceInterface
{
    /**
     * @inheritDoc
     */
    public function notifyExperienceGained(User $user, int $amount): void
    {
        $notification = [
            'userId' => (string)$user->id,
            'icon' => 'FaAnglesUp',
            'actionUrl' => '/profile',
            'title' => "You received $amount experience.",
        ];

        $user->notify(new DatabaseNotification($notification));
    }

    /**
     * @inheritDoc
     */
    public function notifyLevelUp(User $user, Level $level): void
    {
        $notification = [
            'userId' => (string)$user->id,
            'icon' => 'FaLevelUpAlt',
            'actionUrl' => '/profile/?section=levels',
            'title' => "Congratulations for reaching a new level! You are now on Level {$level->level}.",
        ];

        $user->notify(new DatabaseNotification($notification));
    }
}

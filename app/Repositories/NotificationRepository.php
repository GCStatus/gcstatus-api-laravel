<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;
use App\Contracts\Repositories\NotificationRepositoryInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function all(User $user): Collection
    {
        return $user->notifications;
    }

    /**
     * @inheritDoc
     */
    public function findOrFail(string $id): DatabaseNotification
    {
        return DatabaseNotification::findOrFail($id);
    }

    /**
     * @inheritDoc
     */
    public function markAsRead(DatabaseNotification $notification): void
    {
        $notification->markAsRead();
    }

    /**
     * @inheritDoc
     */
    public function markAsUnread(DatabaseNotification $notification): void
    {
        $notification->markAsUnread();
    }

    /**
     * @inheritDoc
     */
    public function markAllAsRead(User $user): void
    {
        /** @var \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications */
        $notifications = $user->notifications;

        $notifications->markAsRead();
    }

    /**
     * Remove a notification.
     *
     * @param \Illuminate\Notifications\DatabaseNotification $notification
     * @return void
     */
    public function remove(DatabaseNotification $notification): void
    {
        $notification->delete();
    }

    /**
     * Remove all notifications.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function removeAll(User $user): void
    {
        /** @var \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications */
        $notifications = $user->notifications;

        $notifications->each->delete();
    }
}

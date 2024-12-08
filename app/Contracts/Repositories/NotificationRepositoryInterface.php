<?php

namespace App\Contracts\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;

interface NotificationRepositoryInterface
{
    /**
     * Get all user notifications for given user.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Notifications\DatabaseNotification>
     */
    public function all(User $user): Collection;

    /**
     * Find or fail notification by id.
     *
     * @param string $id
     * @return \Illuminate\Notifications\DatabaseNotification
     */
    public function findOrFail(string $id): DatabaseNotification;

    /**
     * Mark notification as read.
     *
     * @param \Illuminate\Notifications\DatabaseNotification $notification
     * @return void
     */
    public function markAsRead(DatabaseNotification $notification): void;

    /**
     * Mark notification as unread.
     *
     * @param \Illuminate\Notifications\DatabaseNotification $notification
     * @return void
     */
    public function markAsUnread(DatabaseNotification $notification): void;

    /**
     * Mark all notification as read.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function markAllAsRead(User $user): void;

    /**
     * Remove a notification.
     *
     * @param \Illuminate\Notifications\DatabaseNotification $notification
     * @return void
     */
    public function remove(DatabaseNotification $notification): void;

    /**
     * Remove all notifications.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function removeAll(User $user): void;
}

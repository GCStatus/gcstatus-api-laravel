<?php

namespace App\Contracts\Services;

use Illuminate\Database\Eloquent\Collection;

interface NotificationServiceInterface
{
    /**
     * Get all auth user notifications.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Notifications\DatabaseNotification>
     */
    public function all(): Collection;

    /**
     * Mark notification as read.
     *
     * @param string $id
     * @return void
     */
    public function markAsRead(string $id): void;

    /**
     * Mark notification as unread.
     *
     * @param string $id
     * @return void
     */
    public function markAsUnread(string $id): void;

    /**
     * Mark all notification as read.
     *
     * @return void
     */
    public function markAllAsRead(): void;

    /**
     * Remove a given notification.
     *
     * @param string $id
     * @return void
     */
    public function remove(string $id): void;

    /**
     * Remove all auth user notifications.
     *
     * @return void
     */
    public function removeAll(): void;
}

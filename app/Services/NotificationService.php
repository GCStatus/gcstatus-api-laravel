<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;
use App\Contracts\Repositories\NotificationRepositoryInterface;
use App\Exceptions\Notification\NotificationDoesntBelongsToUserException;
use App\Contracts\Services\{
    AuthServiceInterface,
    NotificationServiceInterface,
};

class NotificationService implements NotificationServiceInterface
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * The notification repository.
     *
     * @var \App\Contracts\Repositories\NotificationRepositoryInterface
     */
    private NotificationRepositoryInterface $notificationRepository;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authService = app(AuthServiceInterface::class);
        $this->notificationRepository = app(NotificationRepositoryInterface::class);
    }

    /**
     * Get all auth user notifications.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \Illuminate\Notifications\DatabaseNotification>
     */
    public function all(): Collection
    {
        /** @var \App\Models\User $user */
        $user = $this->authService->getAuthUser();

        return $this->notificationRepository->all($user);
    }

    /**
     * Mark notification as read.
     *
     * @param string $id
     * @return void
     */
    public function markAsRead(string $id): void
    {
        /** @var \Illuminate\Notifications\DatabaseNotification $notification */
        $notification = $this->notificationRepository->findOrFail($id);

        $this->assertCanAct($notification);

        $this->notificationRepository->markAsRead($notification);
    }

    /**
     * Mark notification as unread.
     *
     * @param string $id
     * @return void
     */
    public function markAsUnread(string $id): void
    {
        /** @var \Illuminate\Notifications\DatabaseNotification $notification */
        $notification = $this->notificationRepository->findOrFail($id);

        $this->assertCanAct($notification);

        $this->notificationRepository->markAsUnread($notification);
    }

    /**
     * Mark all notification as read.
     *
     * @return void
     */
    public function markAllAsRead(): void
    {
        /** @var \App\Models\User $user */
        $user = $this->authService->getAuthUser();

        $this->notificationRepository->markAllAsRead($user);
    }

    /**
     * Remove a given notification.
     *
     * @param string $id
     * @return void
     */
    public function remove(string $id): void
    {
        /** @var \Illuminate\Notifications\DatabaseNotification $notification */
        $notification = $this->notificationRepository->findOrFail($id);

        $this->assertCanAct($notification);

        $this->notificationRepository->remove($notification);
    }

    /**
     * Remove all auth user notifications.
     *
     * @return void
     */
    public function removeAll(): void
    {
        /** @var \App\Models\User $user */
        $user = $this->authService->getAuthUser();

        $this->notificationRepository->removeAll($user);
    }

    /**
     * Assert can act with notification.
     *
     * @param \Illuminate\Notifications\DatabaseNotification $notification
     * @return void
     */
    private function assertCanAct(DatabaseNotification $notification): void
    {
        /** @var int $userId */
        $userId = $this->authService->getAuthId();

        /** @var int */
        $notifiableId = $notification->notifiable_id;

        if ((int)$userId !== (int)$notifiableId) {
            throw new NotificationDoesntBelongsToUserException();
        }
    }
}

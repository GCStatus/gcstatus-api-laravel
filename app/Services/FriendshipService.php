<?php

namespace App\Services;

use App\Models\Friendship;
use App\Contracts\Repositories\FriendshipRepositoryInterface;
use App\Contracts\Services\{
    FriendshipServiceInterface,
    FriendshipNotificationServiceInterface,
};

class FriendshipService extends AbstractService implements FriendshipServiceInterface
{
    /**
     * The friendship notification service.
     *
     * @var \App\Contracts\Services\FriendshipNotificationServiceInterface
     */
    private FriendshipNotificationServiceInterface $friendshipNotificationService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->friendshipNotificationService = app(FriendshipNotificationServiceInterface::class);
    }

    /**
     * The friendship repository.
     *
     * @return \App\Contracts\Repositories\FriendshipRepositoryInterface
     */
    public function repository(): FriendshipRepositoryInterface
    {
        return app(FriendshipRepositoryInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function exists(mixed $userId, mixed $friendId): bool
    {
        return $this->repository()->friendshipExists($userId, $friendId);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): Friendship
    {
        /** @var \App\Models\Friendship $friendship */
        $friendship = $this->repository()->create($data);

        $this->friendshipNotificationService->notifyNewFriendship($friendship);

        return $friendship;
    }
}

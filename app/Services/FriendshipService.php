<?php

namespace App\Services;

use App\Contracts\Repositories\FriendshipRepositoryInterface;
use App\Contracts\Services\{
    AuthServiceInterface,
    FriendshipServiceInterface,
};

class FriendshipService extends AbstractService implements FriendshipServiceInterface
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authService = app(AuthServiceInterface::class);
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
    public function exists(mixed $friendId): bool
    {
        $userId = $this->authService->getAuthId();

        return $this->repository()->friendshipExists($userId, $friendId);
    }
}

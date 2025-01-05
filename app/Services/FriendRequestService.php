<?php

namespace App\Services;

use App\Models\FriendRequest;
use App\Contracts\Repositories\FriendRequestRepositoryInterface;
use App\Contracts\Services\{
    AuthServiceInterface,
    FriendshipServiceInterface,
    FriendRequestServiceInterface,
};

class FriendRequestService extends AbstractService implements FriendRequestServiceInterface
{
    /**
     * The auth service.
     *
     * @var \App\Contracts\Services\AuthServiceInterface
     */
    private AuthServiceInterface $authService;

    /**
     * The friendship service.
     *
     * @var \App\Contracts\Services\FriendshipServiceInterface
     */
    private FriendshipServiceInterface $friendshipService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authService = app(AuthServiceInterface::class);
        $this->friendshipService = app(FriendshipServiceInterface::class);
    }

    /**
     * The friend request repository.
     *
     * @return \App\Contracts\Repositories\FriendRequestRepositoryInterface
     */
    public function repository(): FriendRequestRepositoryInterface
    {
        return app(FriendRequestRepositoryInterface::class);
    }

    /**
     * @inheritDoc
     */
    public function send(mixed $addresseeId): void
    {
        $userId = $this->authService->getAuthId();

        $this->assertCanSend($userId, $addresseeId);

        $this->repository()->create([
            'addressee_id' => $addresseeId,
            'requester_id' => $this->authService->getAuthId(),
        ]);
    }

    /**
     * Accept a friend request.
     *
     * @param mixed $id
     * @return void
     */
    public function accept(mixed $id): void
    {
        $userId = $this->authService->getAuthId();

        /** @var \App\Models\FriendRequest $friendRequest */
        $friendRequest = $this->repository()->findOrFail($id);

        $this->assertCanAct($userId, $friendRequest);

        $this->friendshipService->create([
            'user_id' => $userId,
            'friend_id' => '',
        ]);

        $this->friendshipService->create([
            'user_id' => '',
            'friend_id' => $userId,
        ]);
    }

    /**
     * Decline a friend request.
     *
     * @param mixed $id
     * @return void
     */
    public function decline(mixed $id): void
    {
        $repository = $this->repository();

        $userId = $this->authService->getAuthId();

        /** @var \App\Models\FriendRequest $friendRequest */
        $friendRequest = $repository->findOrFail($id);

        $this->assertCanAct($userId, $friendRequest);

        $friendRequest->delete();
    }

    /**
     * Assert can send a friend request.
     *
     * @param mixed $userId
     * @param mixed $addresseeId
     * @return void
     */
    private function assertCanSend(mixed $userId, mixed $addresseeId): void
    {
        if ($this->repository()->exists($userId, $addresseeId)) {
            throw new \Exception('');
        }
    }

    /**
     * Assert can act.
     *
     * @param mixed $userId
     * @param \App\Models\FriendRequest $friendRequest
     * @return void
     */
    private function assertCanAct(mixed $userId, FriendRequest $friendRequest): void
    {
        if ($friendRequest->addressee_id != $userId) {
            throw new \Exception('');
        }
    }
}

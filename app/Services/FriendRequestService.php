<?php

namespace App\Services;

use App\Models\FriendRequest;
use App\Contracts\Repositories\FriendRequestRepositoryInterface;
use App\Contracts\Services\{
    AuthServiceInterface,
    FriendshipServiceInterface,
    FriendRequestServiceInterface,
};
use App\Exceptions\FriendRequest\{
    NotFriendRequestReceiverException,
    FriendRequestAlreadyExistsException,
    FriendRequestCantBeSentToYouException,
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

        $repository = $this->repository();

        $repository->create([
            'requester_id' => $userId,
            'addressee_id' => $addresseeId,
        ]);

        if ($repository->reciprocalRequestExists($userId, $addresseeId)) {
            $this->createMutualFriendship($userId, $addresseeId);
        }
    }

    /**
     * @inheritDoc
     */
    public function accept(mixed $id): void
    {
        $userId = $this->authService->getAuthId();

        /** @var \App\Models\FriendRequest $friendRequest */
        $friendRequest = $this->repository()->findOrFail($id);

        $this->assertCanAct($userId, $friendRequest);

        $this->createMutualFriendship($userId, $friendRequest->requester_id);
    }

    /**
     * @inheritDoc
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
     * Create the mutual friendship.
     *
     * @param mixed $userId
     * @param mixed $friendId
     * @return void
     */
    private function createMutualFriendship(mixed $userId, mixed $friendId): void
    {
        if (!$this->friendshipService->exists($userId, $friendId)) {
            $this->friendshipService->create([
                'user_id' => $userId,
                'friend_id' => $friendId,
            ]);

            $this->friendshipService->create([
                'user_id' => $friendId,
                'friend_id' => $userId,
            ]);
        }

        $this->repository()->deleteReciprocalRequests($userId, $friendId);
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
        if ($userId === $addresseeId) {
            throw new FriendRequestCantBeSentToYouException();
        }

        if ($this->repository()->exists($userId, $addresseeId)) {
            throw new FriendRequestAlreadyExistsException();
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
            throw new NotFriendRequestReceiverException();
        }
    }
}

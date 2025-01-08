<?php

namespace App\Http\Controllers;

use App\Contracts\Services\FriendRequestServiceInterface;
use App\Http\Requests\FriendRequest\FriendRequestStoreRequest;

class FriendRequestController extends Controller
{
    /**
     * The friend request service.
     *
     * @var \App\Contracts\Services\FriendRequestServiceInterface
     */
    private FriendRequestServiceInterface $friendRequestService;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->friendRequestService = app(FriendRequestServiceInterface::class);
    }

    /**
     * Send a friend request.
     *
     * @param \App\Http\Requests\FriendRequest\FriendRequestStoreRequest $request
     * @return void
     */
    public function send(FriendRequestStoreRequest $request): void
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        $this->friendRequestService->send($data['addressee_id']);
    }

    /**
     * Accept a friend request.
     *
     * @param mixed $id
     * @return void
     */
    public function accept(mixed $id): void
    {
        $this->friendRequestService->accept($id);
    }

    /**
     * Decline a friend request.
     *
     * @param mixed $id
     * @return void
     */
    public function decline(mixed $id): void
    {
        $this->friendRequestService->decline($id);
    }
}

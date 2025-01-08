<?php

namespace App\Repositories;

use App\Models\FriendRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Contracts\Repositories\FriendRequestRepositoryInterface;

class FriendRequestRepository extends AbstractRepository implements FriendRequestRepositoryInterface
{
    /**
     * The friend request model.
     *
     * @return \App\Models\FriendRequest
     */
    public function model(): FriendRequest
    {
        return new FriendRequest();
    }

    /**
     * @inheritDoc
     */
    public function exists(mixed $userId, mixed $addresseeId): bool
    {
        return $this->model()
            ->query()
            ->where('requester_id', $userId)
            ->where('addressee_id', $addresseeId)
            ->exists();
    }

    /**
     * @inheritDoc
     */
    public function reciprocalRequestExists(mixed $userId, mixed $addresseeId): bool
    {
        return $this->model()
            ->query()
            ->where('requester_id', $addresseeId)
            ->where('addressee_id', $userId)
            ->exists();
    }

    /**
     * @inheritDoc
     */
    public function deleteReciprocalRequests(mixed $userId, mixed $friendId): void
    {
        $this->model()
            ->query()
            ->where(function (Builder $query) use ($userId, $friendId) {
                $query->where('requester_id', $userId)->where('addressee_id', $friendId);
            })->orWhere(function (Builder $query) use ($userId, $friendId) {
                $query->where('requester_id', $friendId)->where('addressee_id', $userId);
            })->delete();
    }
}

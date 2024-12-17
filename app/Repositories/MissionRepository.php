<?php

namespace App\Repositories;

use App\Models\{User, Mission};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Contracts\Repositories\MissionRepositoryInterface;

class MissionRepository implements MissionRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function allForUser(User $user): LengthAwarePaginator
    {
        return Mission::query()->with([
            'status',
            'rewards.rewardable',
            'userMission' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            },
            'requirements.userProgress' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            },
        ])->where(function (Builder $where) use ($user) {
            $where->where('for_all', true)->orWhereHas('users', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            });
        })->paginate(10);
    }

    /**
     * @inheritDoc
     */
    public function findOrFail(mixed $id): Mission
    {
        return Mission::findOrFail($id);
    }
}

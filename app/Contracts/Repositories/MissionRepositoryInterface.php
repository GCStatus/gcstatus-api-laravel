<?php

namespace App\Contracts\Repositories;

use App\Models\{User, Mission};
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MissionRepositoryInterface
{
    /**
     * Get all missions for user.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Models\Mission>
     */
    public function allForUser(User $user): LengthAwarePaginator;

    /**
     * Find mission or fail if don't.
     *
     * @param mixed $id
     * @return \App\Models\Mission
     */
    public function findOrFail(mixed $id): Mission;
}

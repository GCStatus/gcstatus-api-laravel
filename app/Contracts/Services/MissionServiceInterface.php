<?php

namespace App\Contracts\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MissionServiceInterface
{
    /**
     * Get all missions for user.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Models\Mission>
     */
    public function allForUser(): LengthAwarePaginator;

    /**
     * Complete some given mission for user.
     *
     * @param mixed $id
     * @return void
     */
    public function complete(mixed $id): void;
}

<?php

namespace App\Contracts\Repositories;

interface UserTitleRepositoryInterface extends AbstractRepositoryInterface
{
    /**
     * Toggle given title for the given user.
     *
     * @param int $userId
     * @param mixed $titleId
     * @return void
     */
    public function toggleTitle(int $userId, mixed $titleId): void;
}

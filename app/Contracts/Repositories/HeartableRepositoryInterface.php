<?php

namespace App\Contracts\Repositories;

use App\Models\Heartable;

interface HeartableRepositoryInterface extends AbstractRepositoryInterface
{
    /**
     * Find by given user.
     *
     * @param mixed $userId
     * @param array<string, mixed> $data
     * @return ?\App\Models\Heartable
     */
    public function findByUser(mixed $userId, array $data): ?Heartable;
}

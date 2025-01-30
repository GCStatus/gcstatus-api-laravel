<?php

namespace App\Repositories;

use App\Models\Heartable;
use App\Contracts\Repositories\HeartableRepositoryInterface;

class HeartableRepository extends AbstractRepository implements HeartableRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function model(): Heartable
    {
        return new Heartable();
    }

    /**
     * @inheritDoc
     */
    public function findByUser(mixed $userId, array $data): ?Heartable
    {
        return $this->model()
            ->query()
            ->where('user_id', $userId)
            ->where('heartable_id', $data['heartable_id'])
            ->where('heartable_type', $data['heartable_type'])
            ->first();
    }
}

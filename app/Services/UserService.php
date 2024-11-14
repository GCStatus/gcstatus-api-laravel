<?php

namespace App\Services;

use App\Models\User;
use App\Contracts\Services\UserServiceInterface;
use App\Contracts\Repositories\UserRepositoryInterface;

class UserService extends AbstractService implements UserServiceInterface
{
    /**
     * Get the repository instance.
     *
     * @return \App\Contracts\Repositories\UserRepositoryInterface
     */
    public function repository(): UserRepositoryInterface
    {
        return app(UserRepositoryInterface::class);
    }

    /**
     * Get the first user or create if doesn't exist.
     *
     * @param array<string, mixed> $searchable
     * @param array<string, mixed> $creatable
     * @return \App\Models\User
     */
    public function firstOrCreate(array $searchable, array $creatable): User
    {
        return $this->repository()->firstOrCreate($searchable, $creatable);
    }
}

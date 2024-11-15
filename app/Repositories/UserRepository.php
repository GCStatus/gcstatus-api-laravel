<?php

namespace App\Repositories;

use App\Models\User;
use App\Contracts\Repositories\UserRepositoryInterface;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    /**
     * Get the model instance.
     *
     * @return \App\Models\User
     */
    public function model(): User
    {
        return new User();
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
        return $this->model()->firstOrCreate($searchable, $creatable);
    }
}

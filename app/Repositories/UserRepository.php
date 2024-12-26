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
     * @inheritDoc
     */
    public function firstOrCreate(array $searchable, array $creatable): User
    {
        return $this->model()->firstOrCreate($searchable, $creatable);
    }

    /**
     * @inheritDoc
     */
    public function addExperience(User $user, int $amount): void
    {
        $user->increment('experience', $amount);
    }
}

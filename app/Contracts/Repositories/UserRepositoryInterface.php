<?php

namespace App\Contracts\Repositories;

use App\Models\User;

interface UserRepositoryInterface extends AbstractRepositoryInterface
{
    /**
     * Get the first user or create if doesn't exist.
     *
     * @param array<string, mixed> $searchable
     * @param array<string, mixed> $creatable
     * @return \App\Models\User
     */
    public function firstOrCreate(array $searchable, array $creatable = []): User;

    /**
     * Increment experience for given user.
     *
     * @param \App\Models\User $user
     * @param int $amount
     * @return void
     */
    public function addExperience(User $user, int $amount): void;
}

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
    public function firstOrCreate(array $searchable, array $creatable): User;

    /**
     * Increment experience for given user.
     *
     * @param mixed $id
     * @param int $amount
     * @return void
     */
    public function addExperience(mixed $id, int $amount): void;
}

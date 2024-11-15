<?php

namespace App\Contracts\Services;

use App\Models\User;

interface UserServiceInterface extends AbstractServiceInterface
{
    /**
     * Get the first user or create if doesn't exist.
     *
     * @param array<string, mixed> $searchable
     * @param array<string, mixed> $creatable
     * @return \App\Models\User
     */
    public function firstOrCreate(array $searchable, array $creatable): User;
}

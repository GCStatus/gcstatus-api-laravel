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
    /**
     * Update the user password.
     *
     * @param \App\Models\User $user
     * @param string $old_password
     * @param string $password
     * @return void
     */
    public function updatePassword(User $user, string $old_password, string $password): void;

    /**
     * Update the user sensitive data.
     *
     * @param \App\Models\User $user
     * @param array<string, string> $data
     * @return void
     */
    public function updateSensitives(User $user, array $data): void;

    /**
     * Increment experience for given user.
     *
     * @param \App\Models\User $user
     * @param int $amount
     * @return void
     */
    public function addExperience(User $user, int $amount): void;
}

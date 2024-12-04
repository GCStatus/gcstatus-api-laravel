<?php

namespace App\Contracts\Repositories;

interface UserMissionProgressRepositoryInterface
{
    /**
     * Update or create a given progress.
     *
     * @param array<string, mixed> $verifiable
     * @param array<string, mixed> $updatable
     * @return void
     */
    public function updateOrCreate(array $verifiable, array $updatable): void;
}

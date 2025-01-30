<?php

namespace App\Contracts\Services;

interface HeartableServiceInterface extends AbstractServiceInterface
{
    /**
     * Toggle heartable for auth user.
     *
     * @param array<string, mixed> $data
     * @return void
     */
    public function toggle(array $data): void;
}

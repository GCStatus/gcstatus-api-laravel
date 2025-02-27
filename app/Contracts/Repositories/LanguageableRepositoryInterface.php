<?php

namespace App\Contracts\Repositories;

interface LanguageableRepositoryInterface extends AbstractRepositoryInterface
{
    /**
     * Check if exists for payload.
     *
     * @param array<string, mixed> $data
     * @return bool
     */
    public function existsForPayload(array $data): bool;
}

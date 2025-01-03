<?php

namespace App\Contracts\Services\Validation;

interface SteamResponseValidatorInterface
{
    /**
     * Validate a steam fetch response.
     *
     * @param string $appId
     * @param array<string, mixed> $details
     * @return void
     */
    public function validate(string $appId, array $details): void;
}

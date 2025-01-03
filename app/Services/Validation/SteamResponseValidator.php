<?php

namespace App\Services\Validation;

use App\Exceptions\GenericException;
use App\Contracts\Services\Validation\SteamResponseValidatorInterface;

class SteamResponseValidator implements SteamResponseValidatorInterface
{
    /**
     * @inheritDoc
     */
    public function validate(string $appId, array $details): void
    {
        if (!isset($details[$appId]['success']) || !$details[$appId]['success']) {
            throw new GenericException("Failed to fetch data for app ID: $appId", 400);
        }
    }
}

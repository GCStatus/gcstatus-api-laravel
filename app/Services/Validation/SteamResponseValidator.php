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
        if (!isset($details[$appId])) {
            throw new GenericException("Invalid response: Missing app ID ($appId) in Steam API response.", 400);
        }

        /** @var array<string, string> $appDetail */
        $appDetail = $details[$appId];

        if (!isset($appDetail['success']) || !$appDetail['success']) {
            $errorMessage = (string)($appDetail['message'] ?? 'Unknown error');

            throw new GenericException("Steam API request failed for app ID: $appId. Error: $errorMessage", 400);
        }

        if (!isset($appDetail['data'])) {
            throw new GenericException("Steam API response for app ID: $appId is missing 'data' key.", 400);
        }
    }
}

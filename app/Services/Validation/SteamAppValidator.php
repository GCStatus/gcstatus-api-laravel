<?php

namespace App\Services\Validation;

use InvalidArgumentException;
use App\Contracts\Services\Validation\SteamAppValidatorInterface;

class SteamAppValidator implements SteamAppValidatorInterface
{
    /**
     * @inheritDoc
     */
    public static function validate(array $data): void
    {
        $requiredFields = [
            'type',
            'name',
            'is_free',
            'steam_appid',
            'release_date',
            'background_raw',
            'about_the_game',
            'short_description',
            'detailed_description',
        ];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                logService()->withContext('Failed to validate steam app: ', [
                    'data' => $data,
                    'missing_field' => $field,
                ]);

                throw new InvalidArgumentException("Missing required field: $field");
            }
        }
    }
}

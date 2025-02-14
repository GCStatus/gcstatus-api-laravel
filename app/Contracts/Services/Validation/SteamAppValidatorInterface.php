<?php

namespace App\Contracts\Services\Validation;

interface SteamAppValidatorInterface
{
    /**
     * Validate the Steam App data.
     *
     * @param array<string, mixed> $data
     * @return void
     */
    public static function validate(array $data): void;
}

<?php

namespace App\Contracts\Services\Validation;

interface IdentifierValidatorInterface
{
    /**
     * Validate identifier contracts.
     *
     * @param string $identifier
     * @return bool
     */
    public function validate(string $identifier): bool;
}

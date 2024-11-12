<?php

namespace App\Services\Validation;

use Illuminate\Support\Facades\Validator;
use App\Contracts\Services\Validation\IdentifierValidatorInterface;

class EmailValidator implements IdentifierValidatorInterface
{
    /**
     * Validate the identifier as email.
     *
     * @param string $identifier
     * @return bool
     */
    public function validate(string $identifier): bool
    {
        return Validator::make(
            ['identifier' => $identifier],
            ['identifier' => ['string', 'email:rfc,dns']],
        )->passes();
    }
}

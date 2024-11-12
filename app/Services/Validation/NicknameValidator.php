<?php

namespace App\Services\Validation;

use Illuminate\Support\Facades\Validator;
use App\Contracts\Services\Validation\IdentifierValidatorInterface;

class NicknameValidator implements IdentifierValidatorInterface
{
    /**
     * Validate the identifier as nickname.
     *
     * @param string $identifier
     * @return bool
     */
    public function validate(string $identifier): bool
    {
        return Validator::make(
            ['identifier' => $identifier],
            ['identifier' => ['string', 'regex:/^[a-zA-Z0-9._-]+$/', 'not_regex:/^\s*$/']],
        )->passes();
    }
}

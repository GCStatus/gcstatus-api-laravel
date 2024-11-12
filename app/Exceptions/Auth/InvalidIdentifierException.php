<?php

namespace App\Exceptions\Auth;

use App\Exceptions\UnprocessableEntityException;

class InvalidIdentifierException extends UnprocessableEntityException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'The provided identifier is invalid. Please, use your email or nickname to proceed.';
}

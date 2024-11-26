<?php

namespace App\Exceptions\Password;

use App\Exceptions\BadRequestException;

class CurrentPasswordDoesNotMatchException extends BadRequestException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'Your current password does not match.';
}

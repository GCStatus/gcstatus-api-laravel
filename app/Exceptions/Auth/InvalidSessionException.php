<?php

namespace App\Exceptions\Auth;

use App\Exceptions\UnauthorizedException;

class InvalidSessionException extends UnauthorizedException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'We could not authenticate your user. Please, try to login again.';
}

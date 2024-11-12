<?php

namespace App\Exceptions\Auth;

use App\Exceptions\UnauthorizedException;

class InvalidUserException extends UnauthorizedException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'We could not find an user with this credentials. Please, double check it and try again!';
}

<?php

namespace App\Exceptions\ResetPassword;

use App\Exceptions\BadRequestException;

class InvalidTokenException extends BadRequestException
{
    /**
     * The response code.
     *
     * @var string
     */
    protected $message = 'We could not validate your reset password request. Please, try again later.';
}

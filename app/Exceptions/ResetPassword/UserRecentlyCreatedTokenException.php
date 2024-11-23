<?php

namespace App\Exceptions\ResetPassword;

use App\Exceptions\BadRequestException;

class UserRecentlyCreatedTokenException extends BadRequestException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'You must wait a few seconds to request a password reset again.';
}

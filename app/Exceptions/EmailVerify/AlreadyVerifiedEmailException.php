<?php

namespace App\Exceptions\EmailVerify;

use App\Exceptions\BadRequestException;

class AlreadyVerifiedEmailException extends BadRequestException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'You already verified your email, no one more action is required.';
}

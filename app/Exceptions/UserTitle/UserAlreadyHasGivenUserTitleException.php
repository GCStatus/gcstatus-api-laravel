<?php

namespace App\Exceptions\UserTitle;

use App\Exceptions\ConflictException;

class UserAlreadyHasGivenUserTitleException extends ConflictException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'The user already has the given title.';
}

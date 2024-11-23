<?php

namespace App\Exceptions\User;

use App\Exceptions\NotFoundException;

class UserNotFoundException extends NotFoundException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'The requested user was not found.';
}

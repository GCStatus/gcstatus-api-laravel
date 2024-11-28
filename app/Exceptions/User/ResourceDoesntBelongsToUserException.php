<?php

namespace App\Exceptions\User;

use App\Exceptions\ForbiddenException;

class ResourceDoesntBelongsToUserException extends ForbiddenException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'The resource that you are requesting do not belongs to your user.';
}

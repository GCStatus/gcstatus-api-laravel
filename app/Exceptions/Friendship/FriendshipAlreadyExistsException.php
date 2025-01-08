<?php

namespace App\Exceptions\Friendship;

use App\Exceptions\ConflictException;

class FriendshipAlreadyExistsException extends ConflictException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'You are already friend of the given user!';
}

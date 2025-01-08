<?php

namespace App\Exceptions\FriendRequest;

use App\Exceptions\ConflictException;

class FriendRequestAlreadyExistsException extends ConflictException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'You already sent a friend request for this user. Please, await for the approve or declinal.';
}

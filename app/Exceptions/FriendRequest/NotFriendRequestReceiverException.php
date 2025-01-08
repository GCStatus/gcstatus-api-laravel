<?php

namespace App\Exceptions\FriendRequest;

use App\Exceptions\ForbiddenException;

class NotFriendRequestReceiverException extends ForbiddenException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'You are not the friend request receiver, this action is unauthorized!';
}

<?php

namespace App\Exceptions\FriendRequest;

use App\Exceptions\BadRequestException;

class FriendRequestCantBeSentToYouException extends BadRequestException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'The friend request can not be yourself!';
}

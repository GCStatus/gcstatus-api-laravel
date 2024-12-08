<?php

namespace App\Exceptions\Notification;

use App\Exceptions\ForbiddenException;

class NotificationDoesntBelongsToUserException extends ForbiddenException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'The given notification does not belongs to your user. This action is unauthorized!';
}

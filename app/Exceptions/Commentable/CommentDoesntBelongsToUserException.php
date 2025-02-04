<?php

namespace App\Exceptions\Commentable;

use App\Exceptions\ForbiddenException;

class CommentDoesntBelongsToUserException extends ForbiddenException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'This comment does not belongs to your user. No one action is allowed.';
}

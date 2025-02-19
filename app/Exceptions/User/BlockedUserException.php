<?php

namespace App\Exceptions\User;

use App\Exceptions\ForbiddenException;

class BlockedUserException extends ForbiddenException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'You are blocked from GCStatus. If you do not agree with this, please, contact the support.';
}

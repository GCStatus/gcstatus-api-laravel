<?php

namespace App\Exceptions\Mission;

use App\Exceptions\ForbiddenException;

class UserDoesntBelongsToMissionException extends ForbiddenException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'Ops! Something wrong happened: you can not complete the given mission.';
}

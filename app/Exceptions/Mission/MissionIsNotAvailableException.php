<?php

namespace App\Exceptions\Mission;

use App\Exceptions\BadRequestException;

class MissionIsNotAvailableException extends BadRequestException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'The given mission is not available.';
}

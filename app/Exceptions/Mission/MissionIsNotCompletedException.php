<?php

namespace App\Exceptions\Mission;

use App\Exceptions\BadRequestException;

class MissionIsNotCompletedException extends BadRequestException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'You did not complete this mission yet. Please, double check it and try again later!';
}

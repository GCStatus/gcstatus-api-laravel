<?php

namespace App\Exceptions\Title;

use App\Exceptions\BadRequestException;

class TitleIsUnavailableException extends BadRequestException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'The given title is unavailable!';
}

<?php

namespace App\Exceptions;

class NotFoundException extends StatusCodeException
{
    /**
     * The response code.
     *
     * @var int
     */
    protected $statusCode = 404;
}

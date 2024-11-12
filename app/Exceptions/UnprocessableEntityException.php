<?php

namespace App\Exceptions;

class UnprocessableEntityException extends StatusCodeException
{
    /**
     * The response code.
     *
     * @var int
     */
    protected $statusCode = 422;
}

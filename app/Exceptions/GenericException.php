<?php

namespace App\Exceptions;

class GenericException extends StatusCodeException
{
    /**
     * Create a new class instance.
     *
     * @param string $message
     * @param int $statusCode
     * @return void
     */
    public function __construct(string $message, int $statusCode)
    {
        $this->message = $message;
        $this->statusCode = $statusCode;
    }
}

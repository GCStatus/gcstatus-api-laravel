<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class StatusCodeException extends Exception implements HttpExceptionInterface
{
    /**
     *  The response code.
     *
     *  @var int
     */
    protected $statusCode;

    /**
     *  The response message.
     *
     *  @var string
     */
    protected $message;

    /**
     *  The response exception.
     *
     *  @var array<int, mixed>
     */
    protected $exception;

    /**
     *  The response headers.
     *
     *  @var array<string, mixed>
     */
    protected $headers = [];

    /**
     *  Get the status code.
     *
     *  @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     *  Get the response headers.
     *
     *  @return array<string, mixed>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}

<?php

namespace App\Contracts\Responses;

interface ApiResponseInterface
{
    /**
     * Set the message for the response.
     *
     * @param string $message
     * @return self
     */
    public function setMessage(string $message): self;

    /**
     * Set the content for the response.
     *
     * @param mixed $content
     * @return self
     */
    public function setContent(mixed $content): self;

    /**
     * Generate the structured response.
     *
     * @return array{data: mixed}
     */
    public function toArray(): array;

    /**
     * Generate the structured message response.
     *
     * @return array{data: array{message: string}}
     */
    public function toMessage(): array;
}

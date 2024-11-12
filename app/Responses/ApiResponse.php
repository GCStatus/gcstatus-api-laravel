<?php

namespace App\Responses;

use App\Contracts\Responses\ApiResponseInterface;

class ApiResponse implements ApiResponseInterface
{
    /**
     * The message content.
     *
     * @var string
     */
    public string $message;

    /**
     * The response content.
     *
     * @var mixed
     */
    public mixed $content;

    /**
     * Set the message for the response.
     *
     * @param string $message
     * @return self
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set the content for the response.
     *
     * @param mixed $content
     * @return self
     */
    public function setContent(mixed $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Generate the structured response as an array with data.
     *
     * @return array{data: array{message: string}}
     */
    public function toMessage(): array
    {
        return [
            'data' => [
                'message' => $this->message,
            ]
        ];
    }

    /**
     * Generate the structured response as an array with message.
     *
     * @return array{data: mixed}
     */
    public function toArray(): array
    {
        return [
            'data' => $this->content,
        ];
    }
}

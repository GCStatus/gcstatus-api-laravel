<?php

namespace Tests\Unit\Responses;

use Tests\TestCase;
use App\Contracts\Responses\ApiResponseInterface;

class ApiResponseTest extends TestCase
{
    /**
     * The api response.
     *
     * @var \App\Contracts\Responses\ApiResponseInterface
     */
    private ApiResponseInterface $apiResponse;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->apiResponse = app(ApiResponseInterface::class);
    }

    /**
     * Test if could successfully set and retrieve a response message.
     *
     * @return void
     */
    public function test_if_could_successfully_set_and_retrieve_a_response_message(): void
    {
        $message = 'Testing a message setup.';

        $this->apiResponse->setMessage($message);

        $responseArray = $this->apiResponse->toMessage();
        $this->assertEquals($responseArray['data']['message'], $message);
    }

    /**
     * Test if could successfully set and retrieve a response content.
     *
     * @return void
     */
    public function test_if_could_successfully_set_and_retrieve_a_response_content(): void
    {
        $content = [
            'attribute' => fake()->randomElement(['Test', 123, null]),
        ];

        $this->apiResponse->setContent($content);

        $responseArray = $this->apiResponse->toArray();
        $this->assertEquals($responseArray['data'], $content);
    }
}

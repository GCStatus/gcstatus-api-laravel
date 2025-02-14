<?php

namespace Tests\Unit\Services\Validation;

use Mockery;
use Tests\TestCase;
use App\Exceptions\GenericException;
use App\Contracts\Services\Validation\SteamResponseValidatorInterface;

class SteamResponseValidatorTest extends TestCase
{
    /**
     * The steam response validator.
     *
     * @var \App\Contracts\Services\Validation\SteamResponseValidatorInterface
     */
    private SteamResponseValidatorInterface $steamResponseValidator;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->steamResponseValidator = app(SteamResponseValidatorInterface::class);
    }

    /**
     * Test if validate throws exception on failed response.
     *
     * @return void
     */
    public function test_validate_throws_exception_on_failed_response()
    {
        $this->expectException(GenericException::class);
        $this->expectExceptionMessage('Steam API request failed for app ID: 123. Error: Unknown error');

        $response = [
            '123' => [
                'success' => false,
            ],
        ];

        $this->steamResponseValidator->validate('123', $response);
    }

    /**
     * Test if validate throws exception on missing app id on response.
     *
     * @return void
     */
    public function test_if_validate_throws_exception_on_missing_app_id_on_response(): void
    {
        $this->expectException(GenericException::class);
        $this->expectExceptionMessage('Invalid response: Missing app ID (123) in Steam API response.');

        $response = [
            'empty' => 'response',
        ];

        $this->steamResponseValidator->validate('123', $response); // @phpstan-ignore-line
    }

    /**
     * Test if validate throws exception on missing data key.
     *
     * @return void
     */
    public function test_validate_throws_exception_on_missing_data_key()
    {
        $this->expectException(GenericException::class);
        $this->expectExceptionMessage("Steam API response for app ID: 123 is missing 'data' key.");

        $response = [
            '123' => [
                'success' => true,
            ],
        ];

        $this->steamResponseValidator->validate('123', $response); // @phpstan-ignore-line
    }

    /**
     * Test if validate passes on valid response.
     *
     * @return void
     */
    public function test_validate_passes_on_valid_response(): void
    {
        $response = [
            '123' => [
                'success' => true,
                'data' => ['name' => 'Half-Life'],
            ],
        ];

        $this->steamResponseValidator->validate('123', $response); // @phpstan-ignore-line

        $this->assertTrue(true); // @phpstan-ignore-line
    }

    /**
     * Tear down application tests.
     *
     * @return void
     */
    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}

<?php

namespace Tests\Unit\Requests\Auth;

use App\Http\Requests\Auth\LoginRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;

class LoginRequestTest extends BaseRequestTesting
{
    /**
     * Specify the request class to be tested.
     *
     * @return string
     */
    public function request(): string
    {
        return LoginRequest::class;
    }

    /**
     * Test if can validate the identifier.
     *
     * @return void
     */
    public function test_if_can_validate_the_identifier(): void
    {
        $this->assertFalse($this->validate([
            'identifier' => '',
            'password' => 'password123',
        ]));

        $this->assertFalse($this->validate([
            'identifier' => 123,
            'password' => 'password123',
        ]));

        $this->assertTrue($this->validate([
            'identifier' => fake()->userName(),
            'password' => 'password123',
        ]));

        $this->assertTrue($this->validate([
            'identifier' => fake()->safeEmail(),
            'password' => 'password123',
        ]));
    }

    /**
     * Test if can validate the password.
     *
     * @return void
     */
    public function test_if_can_validate_the_password(): void
    {
        $this->assertFalse($this->validate([
            'identifier' => fake()->userName(),
            'password' => '',
        ]));

        $this->assertFalse($this->validate([
            'identifier' => fake()->userName(),
            'password' => 123,
        ]));

        $this->assertTrue($this->validate([
            'identifier' => fake()->userName(),
            'password' => fake()->word(),
        ]));

        $this->assertTrue($this->validate([
            'identifier' => fake()->userName(),
            'password' => 'password123',
        ]));
    }

    /**
     * Test invalid validation cases.
     *
     * @param array<string, mixed> $data
     * @param array<string, string> $expectedErrors
     * @return void
     */
    #[DataProvider('invalidDataProvider')]
    public function test_invalid_validation_fails(array $data, array $expectedErrors): void
    {
        $this->assertFalse($this->validate($data));

        $errors = $this->getValidationErrors($data);

        foreach ($expectedErrors as $field => $expectedMessage) {
            $this->assertArrayHasKey($field, $errors);
            $this->assertStringContainsString($expectedMessage, $errors[$field][0]);
        }
    }

    /**
     * Data provider for invalid validation cases.
     *
     * @return array<int, array{data: array{password: mixed, identifier: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Both fields missing
            [
                'data' => [
                    'password' => '',
                    'identifier' => '',
                ],
                'expectedErrors' => [
                    'password' => 'The password field is required.',
                    'identifier' => 'The identifier field is required.',
                ],
            ],
            // Case 2: Both fields are the wrong type
            [
                'data' => [
                    'password' => 123,
                    'identifier' => 123,
                ],
                'expectedErrors' => [
                    'password' => 'The password field must be a string.',
                    'identifier' => 'The identifier field must be a string.',
                ],
            ],
        ];
    }
}

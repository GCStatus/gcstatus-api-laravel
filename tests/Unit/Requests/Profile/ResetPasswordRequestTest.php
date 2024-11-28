<?php

namespace Tests\Unit\Requests\Profile;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use App\Http\Requests\Profile\ResetPasswordRequest;

class ResetPasswordRequestTest extends BaseRequestTesting
{
    /**
     * Specify the request class to be tested.
     *
     * @return string
     */
    public function request(): string
    {
        return ResetPasswordRequest::class;
    }

    /**
     * Test if can validate the old password.
     *
     * @return void
     */
    public function test_if_can_validate_the_old_password(): void
    {
        $this->assertFalse($this->validate([
            'old_password' => '',
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ]));

        $this->assertFalse($this->validate([
            'old_password' => 123,
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ]));

        $this->assertFalse($this->validate([
            'old_password' => '1234567',
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ]));

        $this->assertTrue($this->validate([
            'old_password' => '12345678',
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ]));
    }

    /**
     * Test if can validate the password.
     *
     * @return void
     */
    public function test_if_can_validate_the_password(): void
    {
        // Null case
        $this->assertFalse($this->validate([
            'old_password' => '12345678',
            'password' => '',
            'password_confirmation' => ']3N"g&D',
        ]));

        // Without confirmation
        $this->assertFalse($this->validate([
            'old_password' => '12345678',
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => '',
        ]));

        // Unmatch confirmation
        $this->assertFalse($this->validate([
            'old_password' => '12345678',
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7',
        ]));

        // Lower chars
        $this->assertFalse($this->validate([
            'old_password' => '12345678',
            'password' => ']3N"g&D',
            'password_confirmation' => ']3N"g&D',
        ]));

        // Without lowercase
        $this->assertFalse($this->validate([
            'old_password' => '12345678',
            'password' => ']3N"G&D!',
            'password_confirmation' => ']3N"G&D!',
        ]));

        // Without uppercase
        $this->assertFalse($this->validate([
            'old_password' => '12345678',
            'password' => ']3n"g&d!',
            'password_confirmation' => ']3n"g&d!',
        ]));

        // Without symbol
        $this->assertFalse($this->validate([
            'old_password' => '12345678',
            'password' => 'A3n2g0db',
            'password_confirmation' => 'A3n2g0db',
        ]));

        // Without letters
        $this->assertFalse($this->validate([
            'old_password' => '12345678',
            'password' => '23!240!@',
            'password_confirmation' => '23!240!@',
        ]));

        // Without numbers
        $this->assertFalse($this->validate([
            'old_password' => '12345678',
            'password' => 'AaCIS(*@#$',
            'password_confirmation' => 'AaCIS(*@#$',
        ]));

        // Compromised password
        $this->assertFalse($this->validate([
            'old_password' => '12345678',
            'password' => 'Password1234!',
            'password_confirmation' => 'Password1234!',
        ]));

        // Valid password
        $this->assertTrue($this->validate([
            'old_password' => '12345678',
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
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
     * @return array<int, array{data: array{old_password: mixed, password: mixed, password_confirmation?: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing fields
            [
                'data' => [
                    'old_password' => '',
                    'password' => '',
                ],
                'expectedErrors' => [
                    'old_password' => 'The old password field is required.',
                    'password' => 'The password field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'old_password' => 123,
                    'password' => 123,
                ],
                'expectedErrors' => [
                    'old_password' => 'The old password field must be a string.',
                    'password' => 'The password field must be a string.',
                ],
            ],
            // Case 3: Old password is lower than 8 chars
            [
                'data' => [
                    'old_password' => '1234567',
                    'password' => ']3N"g&D8pF7?',
                    'password_confirmation' => ']3N"g&D8pF7?',
                ],
                'expectedErrors' => [
                    'old_password' => 'The old password field must be at least 8 characters.',
                ],
            ],
            // Case 4: Password is unconfirmed
            [
                'data' => [
                    'old_password' => '12345678',
                    'password' => ']3N"g&D8pF7?',
                ],
                'expectedErrors' => [
                    'password' => 'The password field confirmation does not match.',
                ],
            ],
            // Case 5: Unmatch password confirmation
            [
                'data' => [
                    'old_password' => '12345678',
                    'password' => ']3N"g&D8pF7?',
                    'password_confirmation' => ']3N"g&D8pF7',
                ],
                'expectedErrors' => [
                    'password' => 'The password field confirmation does not match.',
                ],
            ],
            // Case 6: Password has no uppercases
            [
                'data' => [
                    'old_password' => '12345678',
                    'password' => ']3n"g&d8pf7?',
                    'password_confirmation' => ']3n"g&d8pf7?',
                ],
                'expectedErrors' => [
                    'password' => 'The password field must contain at least one uppercase and one lowercase letter.',
                ],
            ],
            // Case 7: Password has no lowercases
            [
                'data' => [
                    'old_password' => '12345678',
                    'password' => ']3N"G&D8PF7?',
                    'password_confirmation' => ']3N"G&D8PF7?',
                ],
                'expectedErrors' => [
                    'password' => 'The password field must contain at least one uppercase and one lowercase letter.',
                ],
            ],
            // Case 8: Password has no letters
            [
                'data' => [
                    'old_password' => '12345678',
                    'password' => ']32"!&98#@7?',
                    'password_confirmation' => ']32"!&98#@7?',
                ],
                'expectedErrors' => [
                    'password' => 'The password field must contain at least one uppercase and one lowercase letter.',
                ],
            ],
            // Case 9: Password has no number
            [
                'data' => [
                    'old_password' => '12345678',
                    'password' => ']Aa"!&VD#@%?',
                    'password_confirmation' => ']Aa"!&VD#@%?',
                ],
                'expectedErrors' => [
                    'password' => 'The password field must contain at least one number.',
                ],
            ],
            // Case 10: Password has no symbols
            [
                'data' => [
                    'old_password' => '12345678',
                    'password' => 'NoSymbol123CD',
                    'password_confirmation' => 'NoSymbol123CD',
                ],
                'expectedErrors' => [
                    'password' => 'The password field must contain at least one symbol.',
                ],
            ],
            // Case 11: Password has not at least 8 chars
            [
                'data' => [
                    'old_password' => '12345678',
                    'password' => ']3N"g&D',
                    'password_confirmation' => ']3N"g&D',
                ],
                'expectedErrors' => [
                    'password' => 'The password field must be at least 8 characters.',
                ],
            ],
            // Case 12: Password is compromised
            [
                'data' => [
                    'old_password' => '12345678',
                    'password' => 'Password1234!',
                    'password_confirmation' => 'Password1234!',
                ],
                'expectedErrors' => [
                    'password' => 'The given password has appeared in a data leak. Please choose a different password.',
                ],
            ],
        ];
    }
}

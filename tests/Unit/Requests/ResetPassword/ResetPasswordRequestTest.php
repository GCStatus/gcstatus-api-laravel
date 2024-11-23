<?php

namespace Tests\Unit\Requests\ResetPassword;

use Illuminate\Support\Str;
use Tests\Traits\HasDummyUser;
use Database\Seeders\LevelSeeder;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Requests\ResetPassword\ResetPasswordRequest;

class ResetPasswordRequestTest extends BaseRequestTesting
{
    use HasDummyUser;
    use RefreshDatabase;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed([LevelSeeder::class]);
    }

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
     * Test if can validate the email.
     *
     * @return void
     */
    public function test_if_can_validate_the_email(): void
    {
        $user = $this->createDummyUser([
            'email' => 'valid@gmail.com',
        ]);

        DB::table('password_reset_tokens')->insert([
            'token' => $token = Str::random(40),
            'email' => $user->email,
            'created_at' => now(),
        ]);

        $this->assertFalse($this->validate([
            'email' => '',
            'token' => $token,
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ]));

        $this->assertFalse($this->validate([
            'email' => 123,
            'token' => $token,
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ]));

        $this->assertFalse($this->validate([
            'token' => $token,
            'password' => ']3N"g&D8pF7?',
            'email' => 'ivvalid@gmail.com',
            'password_confirmation' => ']3N"g&D8pF7?',
        ]));

        $this->assertTrue($this->validate([
            'token' => $token,
            'email' => $user->email,
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ]));
    }

    /**
     * Test if can validate the token.
     *
     * @return void
     */
    public function test_if_can_validate_the_token(): void
    {
        $user = $this->createDummyUser([
            'email' => 'valid@gmail.com',
        ]);

        $this->assertFalse($this->validate([
            'token' => '',
            'email' => $user->email,
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ]));

        $this->assertFalse($this->validate([
            'token' => 123,
            'email' => $user->email,
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ]));

        $this->assertTrue($this->validate([
            'email' => $user->email,
            'token' => fake()->word(),
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
        $user = $this->createDummyUser([
            'email' => 'valid@gmail.com',
        ]);

        DB::table('password_reset_tokens')->insert([
            'token' => $token = Str::random(40),
            'email' => $user->email,
            'created_at' => now(),
        ]);

        // Null case
        $this->assertFalse($this->validate([
            'token' => $token,
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => ']3N"g&D',
        ]));

        // Without confirmation
        $this->assertFalse($this->validate([
            'token' => $token,
            'email' => $user->email,
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => '',
        ]));

        // Unmatch confirmation
        $this->assertFalse($this->validate([
            'token' => $token,
            'email' => $user->email,
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7',
        ]));

        // Lower chars
        $this->assertFalse($this->validate([
            'token' => $token,
            'email' => $user->email,
            'password' => ']3N"g&D',
            'password_confirmation' => ']3N"g&D',
        ]));

        // Without lowercase
        $this->assertFalse($this->validate([
            'token' => $token,
            'email' => $user->email,
            'password' => ']3N"G&D!',
            'password_confirmation' => ']3N"G&D!',
        ]));

        // Without uppercase
        $this->assertFalse($this->validate([
            'token' => $token,
            'email' => $user->email,
            'password' => ']3n"g&d!',
            'password_confirmation' => ']3n"g&d!',
        ]));

        // Without symbol
        $this->assertFalse($this->validate([
            'token' => $token,
            'email' => $user->email,
            'password' => 'A3n2g0db',
            'password_confirmation' => 'A3n2g0db',
        ]));

        // Without letters
        $this->assertFalse($this->validate([
            'token' => $token,
            'email' => $user->email,
            'password' => '23!240!@',
            'password_confirmation' => '23!240!@',
        ]));

        // Without numbers
        $this->assertFalse($this->validate([
            'token' => $token,
            'email' => $user->email,
            'password' => 'AaCIS(*@#$',
            'password_confirmation' => 'AaCIS(*@#$',
        ]));

        // Compromised password
        $this->assertFalse($this->validate([
            'token' => $token,
            'email' => $user->email,
            'password' => 'Password1234!',
            'password_confirmation' => 'Password1234!',
        ]));

        // Valid password
        $this->assertTrue($this->validate([
            'token' => $token,
            'email' => $user->email,
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
     * @return array<int, array{data: array{password: mixed, email: mixed, token: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing email
            [
                'data' => [
                    'email' => '',
                    'token' => '',
                    'password' => '',
                ],
                'expectedErrors' => [
                    'email' => 'The email field is required.',
                    'token' => 'The token field is required.',
                    'password' => 'The password field is required.',
                ],
            ],
            // Case 2: Invalid email type
            [
                'data' => [
                    'email' => 123,
                    'token' => 123,
                    'password' => 123,
                ],
                'expectedErrors' => [
                    'email' => 'The email field must be a string.',
                    'token' => 'The token field must be a string.',
                    'password' => 'The password field must be a string.',
                ],
            ],
            // Case 3: Non existant email
            [
                'data' => [
                    'token' => 'valid',
                    'password' => ']3N"g&D8pF7?',
                    'email' => 'inexistent@gmail.com',
                    'password_confirmation' => ']3N"g&D8pF7?',
                ],
                'expectedErrors' => [
                    'email' => 'We could not find any user with the given email. Please, double check it and try again!',
                ],
            ],
            // Case 4: Password is unconfirmed
            [
                'data' => [
                    'token' => 'valid',
                    'email' => 'valid@gmail.com',
                    'password' => ']3N"g&D8pF7?',
                ],
                'expectedErrors' => [
                    'password' => 'The password field confirmation does not match.',
                ],
            ],
            // Case 5: Unmatch password confirmation
            [
                'data' => [
                    'token' => 'valid',
                    'email' => 'valid@gmail.com',
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
                    'token' => 'valid',
                    'email' => 'valid@gmail.com',
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
                    'token' => 'valid',
                    'email' => 'valid@gmail.com',
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
                    'token' => 'valid',
                    'email' => 'valid@gmail.com',
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
                    'token' => 'valid',
                    'email' => 'valid@gmail.com',
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
                    'token' => 'valid',
                    'email' => 'valid@gmail.com',
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
                    'token' => 'valid',
                    'email' => 'valid@gmail.com',
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
                    'token' => 'valid',
                    'email' => 'valid@gmail.com',
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

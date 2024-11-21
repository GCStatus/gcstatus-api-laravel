<?php

namespace Tests\Unit\Requests\Auth;

use Tests\Traits\HasDummyUser;
use Database\Seeders\LevelSeeder;
use App\Http\Requests\Auth\RegisterRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterRequestTest extends BaseRequestTesting
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
        return RegisterRequest::class;
    }

    /**
     * Test if can validate the name.
     *
     * @return void
     */
    public function test_if_can_validate_the_name(): void
    {
        $this->assertFalse($this->validate([
            'name' => '',
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        $this->assertFalse($this->validate([
            'name' => 123,
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->realTextBetween(300, 600),
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->name(),
            'email' => 'valid@gmail.com',
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));
    }

    /**
     * Test if can validate the nickname.
     *
     * @return void
     */
    public function test_if_can_validate_the_nickname(): void
    {
        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'nickname' => '',
            'password' => ']3N"g&D8pF7?',
            'email' => 'valid@gmail.com',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'nickname' => 123,
            'password' => ']3N"g&D8pF7?',
            'email' => 'valid@gmail.com',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'nickname' => fake()->realTextBetween(300, 600),
            'password' => ']3N"g&D8pF7?',
            'email' => 'valid@gmail.com',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->name(),
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'email' => 'valid@gmail.com',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        $user = $this->createDummyUser();

        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'nickname' => $user->nickname,
            'password' => ']3N"g&D8pF7?',
            'email' => 'valid@gmail.com',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));
    }

    /**
     * Test if can validate the birthdate.
     *
     * @return void
     */
    public function test_if_can_validate_the_birthdate(): void
    {
        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'birthdate' => '',
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'email' => 'valid@gmail.com',
            'password_confirmation' => ']3N"g&D8pF7?',
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'email' => 'valid@gmail.com',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(13)->toDateString(),
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'email' => 'valid@gmail.com',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => 'invalid_date',
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'email' => 'valid@gmail.com',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => 1234,
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->name(),
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'email' => 'valid@gmail.com',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
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
            'name' => fake()->name(),
            'nickname' => fake()->userName(),
            'password' => '',
            'email' => 'valid@gmail.com',
            'password_confirmation' => ']3N"g&D',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Without confirmation
        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'email' => 'valid@gmail.com',
            'password_confirmation' => '',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Unmatch confirmation
        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'email' => 'valid@gmail.com',
            'password_confirmation' => ']3N"g&D8pF7',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Lower chars
        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D',
            'email' => 'valid@gmail.com',
            'password_confirmation' => ']3N"g&D',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Without lowercase
        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'nickname' => fake()->userName(),
            'password' => ']3N"G&D!',
            'email' => 'valid@gmail.com',
            'password_confirmation' => ']3N"G&D!',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Without uppercase
        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'nickname' => fake()->userName(),
            'password' => ']3n"g&d!',
            'email' => 'valid@gmail.com',
            'password_confirmation' => ']3n"g&d!',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Without symbol
        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'nickname' => fake()->userName(),
            'password' => 'A3n2g0db',
            'email' => 'valid@gmail.com',
            'password_confirmation' => 'A3n2g0db',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Without letters
        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'nickname' => fake()->userName(),
            'password' => '23!240!@',
            'email' => 'valid@gmail.com',
            'password_confirmation' => '23!240!@',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Without numbers
        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'nickname' => fake()->userName(),
            'password' => 'AaCIS(*@#$',
            'email' => 'valid@gmail.com',
            'password_confirmation' => 'AaCIS(*@#$',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Compromised password
        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'nickname' => fake()->userName(),
            'password' => 'Password1234!',
            'email' => 'valid@gmail.com',
            'password_confirmation' => 'Password1234!',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Valid password
        $this->assertTrue($this->validate([
            'name' => fake()->name(),
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'email' => 'valid@gmail.com',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
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
        if (isset($data['nickname']) && $data['nickname'] === 'duplicatedUser') {
            $this->createDummyUser([
                'nickname' => $data['nickname'],
            ]);
        }
        if (isset($data['email']) && $data['email'] === 'duplicatedEmail') {
            $this->createDummyUser([
                'email' => $data['email'],
            ]);
        }

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
     * @return array<int, array{data: array{password: mixed, password_confirmation?: mixed, birthdate: mixed, nickname: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing fields
            [
                'data' => [
                    'name' => '',
                    'email' => '',
                    'nickname' => '',
                    'password' => '',
                    'birthdate' => '',
                ],
                'expectedErrors' => [
                    'name' => 'The name field is required.',
                    'email' => 'The email field is required.',
                    'nickname' => 'The nickname field is required.',
                    'password' => 'The password field is required.',
                    'birthdate' => 'The birthdate field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'name' => 123,
                    'email' => 123,
                    'nickname' => 123,
                    'password' => 123,
                    'birthdate' => 123,
                ],
                'expectedErrors' => [
                    'name' => 'The name field must be a string.',
                    'email' => 'The email field must be a string.',
                    'nickname' => 'The nickname field must be a string.',
                    'password' => 'The password field must be a string.',
                    'birthdate' => 'The birthdate field must be a valid date.',
                ],
            ],
            // Case 3: Birthdate is lower than 14 years
            [
                'data' => [
                    'name' => fake()->name(),
                    'email' => fake()->safeEmail(),
                    'nickname' => fake()->userName(),
                    'password' => ']3N"g&D8pF7?',
                    'password_confirmation' => ']3N"g&D8pF7?',
                    'birthdate' => today()->subYears(13)->toDateString(),
                ],
                'expectedErrors' => [
                    'birthdate' => 'You should have at least 14 years old to proceed.',
                ],
            ],
            // Case 4: Nickname already taken
            [
                'data' => [
                    'name' => fake()->name(),
                    'email' => fake()->safeEmail(),
                    'nickname' => 'duplicatedUser',
                    'password' => ']3N"g&D8pF7?',
                    'password_confirmation' => ']3N"g&D8pF7?',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'nickname' => 'The providen nickname is already in use.',
                ],
            ],
            // Case 5: Email already taken
            [
                'data' => [
                    'name' => fake()->name(),
                    'email' => 'duplicatedEmail',
                    'nickname' => fake()->userName(),
                    'password' => ']3N"g&D8pF7?',
                    'password_confirmation' => ']3N"g&D8pF7?',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'email' => 'The providen email is already in use.',
                ],
            ],
            // Case 6: Name length is greater than allowed
            [
                'data' => [
                    'name' => fake()->realTextBetween(300, 600),
                    'email' => fake()->safeEmail(),
                    'nickname' => fake()->userName(),
                    'password' => ']3N"g&D8pF7?',
                    'password_confirmation' => ']3N"g&D8pF7?',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'name' => 'Your name must not have more than 255 characters.',
                ],
            ],
            // Case 7: Nickname length is greater than allowed
            [
                'data' => [
                    'name' => fake()->name(),
                    'email' => fake()->safeEmail(),
                    'nickname' => fake()->realTextBetween(300, 600),
                    'password' => ']3N"g&D8pF7?',
                    'password_confirmation' => ']3N"g&D8pF7?',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'nickname' => 'Your nickname must not have more than 255 characters.',
                ],
            ],
            // Case 8: Invalid email format
            [
                'data' => [
                    'name' => fake()->name(),
                    'email' => 'invalidformat.com',
                    'nickname' => fake()->userName(),
                    'password' => ']3N"g&D8pF7?',
                    'password_confirmation' => ']3N"g&D8pF7?',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'email' => 'The email field must be a valid email address.',
                ],
            ],
            // Case 9: Password is unconfirmed
            [
                'data' => [
                    'name' => fake()->name(),
                    'email' => fake()->safeEmail(),
                    'nickname' => fake()->userName(),
                    'password' => ']3N"g&D8pF7?',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'password' => 'The password field confirmation does not match.',
                ],
            ],
            // Case 10: Unmatch password confirmation
            [
                'data' => [
                    'name' => fake()->name(),
                    'email' => fake()->safeEmail(),
                    'nickname' => fake()->userName(),
                    'password' => ']3N"g&D8pF7?',
                    'password_confirmation' => ']3N"g&D8pF7',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'password' => 'The password field confirmation does not match.',
                ],
            ],
            // Case 11: Password has no uppercases
            [
                'data' => [
                    'name' => fake()->name(),
                    'email' => fake()->safeEmail(),
                    'nickname' => fake()->userName(),
                    'password' => ']3n"g&d8pf7?',
                    'password_confirmation' => ']3n"g&d8pf7?',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'password' => 'The password field must contain at least one uppercase and one lowercase letter.',
                ],
            ],
            // Case 12: Password has no lowercases
            [
                'data' => [
                    'name' => fake()->name(),
                    'email' => fake()->safeEmail(),
                    'nickname' => fake()->userName(),
                    'password' => ']3N"G&D8PF7?',
                    'password_confirmation' => ']3N"G&D8PF7?',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'password' => 'The password field must contain at least one uppercase and one lowercase letter.',
                ],
            ],
            // Case 13: Password has no letters
            [
                'data' => [
                    'name' => fake()->name(),
                    'email' => fake()->safeEmail(),
                    'nickname' => fake()->userName(),
                    'password' => ']32"!&98#@7?',
                    'password_confirmation' => ']32"!&98#@7?',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'password' => 'The password field must contain at least one uppercase and one lowercase letter.',
                ],
            ],
            // Case 14: Password has no number
            [
                'data' => [
                    'name' => fake()->name(),
                    'email' => fake()->safeEmail(),
                    'nickname' => fake()->userName(),
                    'password' => ']Aa"!&VD#@%?',
                    'password_confirmation' => ']Aa"!&VD#@%?',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'password' => 'The password field must contain at least one number.',
                ],
            ],
            // Case 15: Password has no symbols
            [
                'data' => [
                    'name' => fake()->name(),
                    'email' => fake()->safeEmail(),
                    'nickname' => fake()->userName(),
                    'password' => 'NoSymbol123CD',
                    'password_confirmation' => 'NoSymbol123CD',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'password' => 'The password field must contain at least one symbol.',
                ],
            ],
            // Case 16: Password has not at least 8 chars
            [
                'data' => [
                    'name' => fake()->name(),
                    'email' => fake()->safeEmail(),
                    'nickname' => fake()->userName(),
                    'password' => ']3N"g&D',
                    'password_confirmation' => ']3N"g&D',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'password' => 'The password field must be at least 8 characters.',
                ],
            ],
            // Case 17: Password is compromised
            [
                'data' => [
                    'name' => fake()->name(),
                    'email' => fake()->safeEmail(),
                    'nickname' => fake()->userName(),
                    'password' => 'Password1234!',
                    'password_confirmation' => 'Password1234!',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'password' => 'The given password has appeared in a data leak. Please choose a different password.',
                ],
            ],
        ];
    }
}

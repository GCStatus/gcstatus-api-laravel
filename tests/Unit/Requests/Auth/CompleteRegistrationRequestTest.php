<?php

namespace Tests\Unit\Requests\Auth;

use Tests\Traits\HasDummyUser;
use Database\Seeders\LevelSeeder;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Requests\Auth\CompleteRegistrationRequest;

class CompleteRegistrationRequestTest extends BaseRequestTesting
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
        return CompleteRegistrationRequest::class;
    }

    /**
     * Test if can validate the nickname.
     *
     * @return void
     */
    public function test_if_can_validate_the_nickname(): void
    {
        $this->assertFalse($this->validate([
            'nickname' => '',
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        $this->assertFalse($this->validate([
            'nickname' => 123,
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        $this->assertTrue($this->validate([
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        $user = $this->createDummyUser();

        $this->assertFalse($this->validate([
            'nickname' => $user->nickname,
            'password' => ']3N"g&D8pF7?',
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
            'birthdate' => '',
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
        ]));

        $this->assertFalse($this->validate([
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => today()->subYears(13)->toDateString(),
        ]));

        $this->assertFalse($this->validate([
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => 'invalid_date',
        ]));

        $this->assertFalse($this->validate([
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7?',
            'birthdate' => 1234,
        ]));

        $this->assertTrue($this->validate([
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
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
            'nickname' => fake()->userName(),
            'password' => '',
            'password_confirmation' => ']3N"g&D',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Without confirmation
        $this->assertFalse($this->validate([
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => '',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Unmatch confirmation
        $this->assertFalse($this->validate([
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
            'password_confirmation' => ']3N"g&D8pF7',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Lower chars
        $this->assertFalse($this->validate([
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D',
            'password_confirmation' => ']3N"g&D',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Without lowercase
        $this->assertFalse($this->validate([
            'nickname' => fake()->userName(),
            'password' => ']3N"G&D!',
            'password_confirmation' => ']3N"G&D!',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Without uppercase
        $this->assertFalse($this->validate([
            'nickname' => fake()->userName(),
            'password' => ']3n"g&d!',
            'password_confirmation' => ']3n"g&d!',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Without symbol
        $this->assertFalse($this->validate([
            'nickname' => fake()->userName(),
            'password' => 'A3n2g0db',
            'password_confirmation' => 'A3n2g0db',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Without letters
        $this->assertFalse($this->validate([
            'nickname' => fake()->userName(),
            'password' => '23!240!@',
            'password_confirmation' => '23!240!@',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Without numbers
        $this->assertFalse($this->validate([
            'nickname' => fake()->userName(),
            'password' => 'AaCIS(*@#$',
            'password_confirmation' => 'AaCIS(*@#$',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Compromised password
        $this->assertFalse($this->validate([
            'nickname' => fake()->userName(),
            'password' => 'Password1234!',
            'password_confirmation' => 'Password1234!',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        // Valid password
        $this->assertTrue($this->validate([
            'nickname' => fake()->userName(),
            'password' => ']3N"g&D8pF7?',
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
                    'nickname' => '',
                    'password' => '',
                    'birthdate' => '',
                ],
                'expectedErrors' => [
                    'nickname' => 'The nickname field is required.',
                    'password' => 'The password field is required.',
                    'birthdate' => 'The birthdate field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'nickname' => 123,
                    'password' => 123,
                    'birthdate' => 123,
                ],
                'expectedErrors' => [
                    'nickname' => 'The nickname field must be a string.',
                    'password' => 'The password field must be a string.',
                    'birthdate' => 'The birthdate field must be a valid date.',
                ],
            ],
            // Case 3: Birthdate is lower than 14 years
            [
                'data' => [
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
                    'nickname' => 'duplicatedUser',
                    'password' => ']3N"g&D8pF7?',
                    'password_confirmation' => ']3N"g&D8pF7?',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'nickname' => 'The providen nickname is already in use.',
                ],
            ],
            // Case 5: Password is unconfirmed
            [
                'data' => [
                    'nickname' => fake()->userName(),
                    'password' => ']3N"g&D8pF7?',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'password' => 'The password field confirmation does not match.',
                ],
            ],
            // Case 6: Unmatch password confirmation
            [
                'data' => [
                    'nickname' => fake()->userName(),
                    'password' => ']3N"g&D8pF7?',
                    'password_confirmation' => ']3N"g&D8pF7',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'password' => 'The password field confirmation does not match.',
                ],
            ],
            // Case 7: Password has no uppercases
            [
                'data' => [
                    'nickname' => fake()->userName(),
                    'password' => ']3n"g&d8pf7?',
                    'password_confirmation' => ']3n"g&d8pf7?',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'password' => 'The password field must contain at least one uppercase and one lowercase letter.',
                ],
            ],
            // Case 8: Password has no lowercases
            [
                'data' => [
                    'nickname' => fake()->userName(),
                    'password' => ']3N"G&D8PF7?',
                    'password_confirmation' => ']3N"G&D8PF7?',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'password' => 'The password field must contain at least one uppercase and one lowercase letter.',
                ],
            ],
            // Case 9: Password has no letters
            [
                'data' => [
                    'nickname' => fake()->userName(),
                    'password' => ']32"!&98#@7?',
                    'password_confirmation' => ']32"!&98#@7?',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'password' => 'The password field must contain at least one uppercase and one lowercase letter.',
                ],
            ],
            // Case 10: Password has no number
            [
                'data' => [
                    'nickname' => fake()->userName(),
                    'password' => ']Aa"!&VD#@%?',
                    'password_confirmation' => ']Aa"!&VD#@%?',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'password' => 'The password field must contain at least one number.',
                ],
            ],
            // Case 11: Password has no symbols
            [
                'data' => [
                    'nickname' => fake()->userName(),
                    'password' => 'NoSymbol123CD',
                    'password_confirmation' => 'NoSymbol123CD',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'password' => 'The password field must contain at least one symbol.',
                ],
            ],
            // Case 12: Password has not at least 8 chars
            [
                'data' => [
                    'nickname' => fake()->userName(),
                    'password' => ']3N"g&D',
                    'password_confirmation' => ']3N"g&D',
                    'birthdate' => today()->subYears(15)->toDateString(),
                ],
                'expectedErrors' => [
                    'password' => 'The password field must be at least 8 characters.',
                ],
            ],
            // Case 13: Password is compromised
            [
                'data' => [
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

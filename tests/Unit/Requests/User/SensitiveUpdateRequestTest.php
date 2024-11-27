<?php

namespace Tests\Unit\Requests\User;

use Tests\Traits\HasDummyUser;
use Database\Seeders\LevelSeeder;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use App\Http\Requests\User\SensitiveUpdateRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SensitiveUpdateRequestTest extends BaseRequestTesting
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
        return SensitiveUpdateRequest::class;
    }

    /**
     * Test if can validate the nickname.
     *
     * @return void
     */
    public function test_if_can_validate_the_nickname(): void
    {
        $user = $this->createDummyUser();

        $this->assertFalse($this->validate([
            'nickname' => null,
            'password' => 'admin1234',
        ]));

        $this->assertFalse($this->validate([
            'nickname' => 123,
            'password' => 'admin1234',
        ]));

        $this->assertFalse($this->validate([
            'nickname' => $user->nickname,
            'password' => 'admin1234',
        ]));

        $this->assertTrue($this->validate([
            'nickname' => '',
            'password' => 'admin1234',
        ]));

        $this->assertTrue($this->validate([
            'password' => 'admin1234',
            'nickname' => fake()->userName(),
        ]));
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

        $this->assertFalse($this->validate([
            'email' => null,
            'password' => 'admin1234',
        ]));

        $this->assertFalse($this->validate([
            'email' => 123,
            'password' => 'admin1234',
        ]));

        $this->assertFalse($this->validate([
            'email' => $user->nickname,
            'password' => 'admin1234',
        ]));

        $this->assertTrue($this->validate([
            'email' => '',
            'password' => 'admin1234',
        ]));

        $this->assertTrue($this->validate([
            'password' => 'admin1234',
            'email' => 'another@gmail.com',
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
            'password' => '',
        ]));

        $this->assertFalse($this->validate([
            'password' => 123,
        ]));

        $this->assertFalse($this->validate([
            'password' => '1234567',
        ]));

        $this->assertTrue($this->validate([
            'password' => 'admin1234',
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
        if (isset($data['email']) && $data['email'] === 'duplicatedEmail@gmail.com') {
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
     * @return array<int, array{data: array{email?: mixed, password: mixed, nickname?: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing fields
            [
                'data' => [
                    'email' => null,
                    'password' => '',
                    'nickname' => null,
                ],
                'expectedErrors' => [
                    'email' => 'The email field must be a string.',
                    'password' => 'The password field is required.',
                    'nickname' => 'The nickname field must be a string.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'email' => 123,
                    'password' => 123,
                    'nickname' => 123,
                ],
                'expectedErrors' => [
                    'email' => 'The email field must be a string.',
                    'password' => 'The password field must be a string.',
                    'nickname' => 'The nickname field must be a string.',
                ],
            ],
            // Case 3: Nickname already in use
            [
                'data' => [
                    'nickname' => 'duplicatedUser',
                    'password' => ']3N"g&D8pF7?',
                ],
                'expectedErrors' => [
                    'nickname' => 'The providen nickname is already in use.',
                ],
            ],
            // Case 4: Email already in use
            [
                'data' => [
                    'email' => 'duplicatedEmail@gmail.com',
                    'password' => ']3N"g&D8pF7?',
                ],
                'expectedErrors' => [
                    'email' => 'The providen email is already in use.',
                ],
            ],
            // Case 5: Email is invalid
            [
                'data' => [
                    'email' => 'invalid.com',
                    'password' => ']3N"g&D8pF7?',
                ],
                'expectedErrors' => [
                    'email' => 'The email field must be a valid email address.',
                ],
            ],
        ];
    }
}

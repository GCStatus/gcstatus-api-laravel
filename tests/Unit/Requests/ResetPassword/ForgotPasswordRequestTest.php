<?php

namespace Tests\Unit\Requests\ResetPassword;

use Tests\Traits\HasDummyUser;
use Database\Seeders\LevelSeeder;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Requests\ResetPassword\ForgotPasswordRequest;

class ForgotPasswordRequestTest extends BaseRequestTesting
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
        return ForgotPasswordRequest::class;
    }

    /**
     * Test if can validate the email.
     *
     * @return void
     */
    public function test_if_can_validate_the_email(): void
    {
        $this->assertFalse($this->validate([
            'email' => '',
        ]));

        $this->assertFalse($this->validate([
            'email' => 123,
        ]));

        $this->assertFalse($this->validate([
            'email' => 'valid@gmail.com',
        ]));

        $user = $this->createDummyUser([
            'email' => 'valid@gmail.com',
        ]);

        $this->assertTrue($this->validate([
            'email' => $user->email,
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
     * @return array<int, array{data: array{email: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing email
            [
                'data' => [
                    'email' => '',
                ],
                'expectedErrors' => [
                    'email' => 'The email field is required.',
                ],
            ],
            // Case 2: Invalid email type
            [
                'data' => [
                    'email' => 123,
                ],
                'expectedErrors' => [
                    'email' => 'The email field must be a string.',
                ],
            ],
            // Case 3: Non existant email
            [
                'data' => [
                    'email' => 'inexistent@gmail.com',
                ],
                'expectedErrors' => [
                    'email' => 'We could not find any user with the given email. Please, double check it and try again!',
                ],
            ],
        ];
    }
}

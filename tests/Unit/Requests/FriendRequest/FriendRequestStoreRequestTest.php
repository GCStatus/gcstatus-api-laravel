<?php

namespace Tests\Unit\Requests\FriendRequest;

use Tests\Traits\HasDummyUser;
use Database\Seeders\LevelSeeder;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Requests\FriendRequest\FriendRequestStoreRequest;

class FriendRequestStoreRequestTest extends BaseRequestTesting
{
    use HasDummyUser;
    use RefreshDatabase;

    /**
     * Setup the request test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed(LevelSeeder::class);
    }

    /**
     * Specify the request class to be tested.
     *
     * @return string
     */
    public function request(): string
    {
        return FriendRequestStoreRequest::class;
    }

    /**
     * Test if can validate the addressee_id.
     *
     * @return void
     */
    public function test_if_can_validate_the_addressee_id(): void
    {
        $this->assertFalse($this->validate([
            'addressee_id' => '',
        ]));

        $this->assertFalse($this->validate([
            'addressee_id' => 123,
        ]));

        $user = $this->createDummyUser();

        $this->assertTrue($this->validate([
            'addressee_id' => $user->id,
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
     * @return array<int, array{data: array{share: mixed, phone: mixed, twitch: mixed, github: mixed, twitter: mixed, youtube: mixed, facebook: mixed, instagram: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing fields
            [
                'data' => [
                    'addressee_id' => '',
                ],
                'expectedErrors' => [
                    'addressee_id' => 'The addressee id field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'addressee_id' => 'invalid',
                ],
                'expectedErrors' => [
                    'addressee_id' => 'The addressee id field must be a number.',
                ],
            ],
            // Case 3: Selected id is doesn't exist on database
            [
                'data' => [
                    'addressee_id' => 2,
                ],
                'expectedErrors' => [
                    'addressee_id' => 'The selected addressee id is invalid.',
                ],
            ],
        ];
    }
}

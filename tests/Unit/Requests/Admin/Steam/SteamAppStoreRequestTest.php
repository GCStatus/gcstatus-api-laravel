<?php

namespace Tests\Unit\Requests\Admin\Steam;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\{HasDummyGame, HasDummyStoreable};
use App\Http\Requests\Admin\Steam\SteamAppStoreRequest;

class SteamAppStoreRequestTest extends BaseRequestTesting
{
    use HasDummyGame;
    use RefreshDatabase;
    use HasDummyStoreable;

    /**
     * @inheritDoc
     */
    public function request(): string
    {
        return SteamAppStoreRequest::class;
    }

    /**
     * Test if can validate the app_id.
     *
     * @return void
     */
    public function test_if_can_validate_the_app_id(): void
    {
        $this->assertFalse($this->validate([
            'app_id' => '',
        ]));

        $game = $this->createDummyGame();

        $this->createDummyStoreable([
            'store_item_id' => '321',
            'storeable_id' => $game->id,
            'storeable_type' => $game::class,
        ]);

        $this->assertFalse($this->validate([
            'app_id' => '321',
        ]));

        $this->assertTrue($this->validate([
            'app_id' => 123,
        ]));

        $this->assertTrue($this->validate([
            'app_id' => '123',
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
        if (isset($data['app_id']) && $data['app_id'] === $storeId = '123') {
            $game = $this->createDummyGame();

            $this->createDummyStoreable([
                'store_item_id' => $storeId,
                'storeable_id' => $game->id,
                'storeable_type' => $game::class,
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
     * @return array<int, array{data: array{app_id: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing fields
            [
                'data' => [
                    'app_id' => '',
                ],
                'expectedErrors' => [
                    'app_id' => 'The app id field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'app_id' => 'invalid',
                ],
                'expectedErrors' => [
                    'app_id' => 'The app id field must be a number.',
                ],
            ],
            // Case 3: Selected id is doesn't exist on database
            [
                'data' => [
                    'app_id' => '123',
                ],
                'expectedErrors' => [
                    'app_id' => 'The given app id is already stored on database.',
                ],
            ],
        ];
    }
}

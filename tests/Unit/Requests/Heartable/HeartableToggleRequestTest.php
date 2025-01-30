<?php

namespace Tests\Unit\Requests\Heartable;

use App\Models\Game;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use App\Http\Requests\Heartable\HeartableToggleRequest;

class HeartableToggleRequestTest extends BaseRequestTesting
{
    /**
     * @inheritDoc
     */
    public function request(): string
    {
        return HeartableToggleRequest::class;
    }

    /**
     * Test if can validate the heartable_id.
     *
     * @return void
     */
    public function test_if_can_validate_the_heartable_id(): void
    {
        $this->assertFalse($this->validate([
            'heartable_id' => '',
            'heartable_type' => Game::class,
        ]));

        $this->assertFalse($this->validate([
            'heartable_type' => Game::class,
        ]));

        $this->assertTrue($this->validate([
            'heartable_id' => fake()->randomDigit(),
            'heartable_type' => Game::class,
        ]));
    }

    /**
     * Test if can validate the heartable_type.
     *
     * @return void
     */
    public function test_if_can_validate_the_heartable_type(): void
    {
        $this->assertFalse($this->validate([
            'heartable_id' => fake()->randomDigit(),
            'heartable_type' => '',
        ]));

        $this->assertFalse($this->validate([
            'heartable_id' => fake()->randomDigit(),
        ]));

        $this->assertTrue($this->validate([
            'heartable_id' => fake()->randomDigit(),
            'heartable_type' => Game::class,
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
     * @return array<int, array{data: array{heartable_id: mixed, heartable_type: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing fields
            [
                'data' => [
                    'heartable_id' => '',
                    'heartable_type' => '',
                ],
                'expectedErrors' => [
                    'heartable_id' => 'The heartable id field is required.',
                    'heartable_type' => 'The heartable type field is required.',
                ],
            ],
        ];
    }
}

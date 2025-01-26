<?php

namespace Tests\Unit\Requests\Game;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use App\Http\Requests\Game\GameFilterAttributeRequest;

class GameFilterAttributeRequestTest extends BaseRequestTesting
{
    /**
     * Specify the request class to be tested.
     *
     * @return string
     */
    public function request(): string
    {
        return GameFilterAttributeRequest::class;
    }

    /**
     * Test if can validate the value field.
     *
     * @return void
     */
    public function test_if_can_validate_the_value_field(): void
    {
        $this->assertFalse($this->validate([
            'value' => '',
            'attribute' => 'tags',
        ]));

        $this->assertFalse($this->validate([
            'value' => 123,
            'attribute' => 'tags',
        ]));

        $this->assertTrue($this->validate([
            'value' => 'a',
            'attribute' => 'tags',
        ]));

        $this->assertTrue($this->validate([
            'value' => fake()->word(),
            'attribute' => 'tags',
        ]));
    }

    /**
     * Test if can validate the attribute field.
     *
     * @return void
     */
    public function test_if_can_validate_the_attribute_field(): void
    {
        $this->assertFalse($this->validate([
            'value' => fake()->word(),
            'attribute' => 'invalid',
        ]));

        $this->assertFalse($this->validate([
            'value' => fake()->word(),
            'attribute' => fake()->word(),
        ]));

        $this->assertTrue($this->validate([
            'value' => fake()->word(),
            'attribute' => 'tags',
        ]));

        $this->assertTrue($this->validate([
            'value' => fake()->word(),
            'attribute' => fake()->randomElement([
                'tags',
                'genres',
                'cracks',
                'crackers',
                'platforms',
                'categories',
                'publishers',
                'developers',
                'protections',
            ]),
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
     * @return array<int, array{data: array{value: mixed, attribute: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing fields
            [
                'data' => [
                    'value' => '',
                    'attribute' => '',
                ],
                'expectedErrors' => [
                    'value' => 'The value field is required.',
                    'attribute' => 'The attribute field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'value' => 123,
                    'attribute' => 123,
                ],
                'expectedErrors' => [
                    'value' => 'The value field must be a string.',
                    'attribute' => 'The attribute field must be a string.',
                ],
            ],
            // Case 3: Invalid attribute
            [
                'data' => [
                    'attribute' => 'invalid',
                    'value' => fake()->word(),
                ],
                'expectedErrors' => [
                    'attribute' => 'This attribute is not accepted!',
                ],
            ],
        ];
    }
}

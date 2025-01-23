<?php

namespace Tests\Unit\Requests\Game;

use App\Http\Requests\Game\GameSearchRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;

class GameSearchRequestTest extends BaseRequestTesting
{
    /**
     * Specify the request class to be tested.
     *
     * @return string
     */
    public function request(): string
    {
        return GameSearchRequest::class;
    }

    /**
     * Test if can validate the q field.
     *
     * @return void
     */
    public function test_if_can_validate_the_q_field(): void
    {
        $this->assertFalse($this->validate([
            'q' => '',
        ]));

        $this->assertFalse($this->validate([
            'q' => 123,
        ]));

        $this->assertFalse($this->validate([
            'q' => 'a',
        ]));

        $this->assertTrue($this->validate([
            'q' => fake()->word(),
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
     * @return array<int, array{data: array{q: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing fields
            [
                'data' => [
                    'q' => '',
                ],
                'expectedErrors' => [
                    'q' => 'The q field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'q' => 123,
                ],
                'expectedErrors' => [
                    'q' => 'The q field must be a string.',
                ],
            ],
            // Case 3: Invalid query size
            [
                'data' => [
                    'q' => 'a',
                ],
                'expectedErrors' => [
                    'q' => 'The q field must be at least 2 characters.',
                ],
            ],
        ];
    }
}

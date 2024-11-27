<?php

namespace Tests\Unit\Requests\User;

use App\Http\Requests\User\BasicUpdateRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;

class BasicUpdateRequestTest extends BaseRequestTesting
{
    /**
     * Specify the request class to be tested.
     *
     * @return string
     */
    public function request(): string
    {
        return BasicUpdateRequest::class;
    }

    /**
     * Test if can validate the nickname.
     *
     * @return void
     */
    public function test_if_can_validate_the_nickname(): void
    {
        $this->assertFalse($this->validate([
            'name' => null,
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        $this->assertFalse($this->validate([
            'name' => 123,
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        $this->assertTrue($this->validate([
            'name' => '',
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        $this->assertTrue($this->validate([
            'birthdate' => today()->subYears(15)->toDateString(),
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->name(),
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
            'birthdate' => null,
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'birthdate' => today()->subYears(13)->toDateString(),
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'birthdate' => 'invalid_date',
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->name(),
            'birthdate' => 1234,
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->name(),
            'birthdate' => '',
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->name(),
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->name(),
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
     * @return array<int, array{data: array{name: mixed, birthdate: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing fields
            [
                'data' => [
                    'name' => null,
                    'birthdate' => null,
                ],
                'expectedErrors' => [
                    'name' => 'The name field must be a string.',
                    'birthdate' => 'The birthdate field must be a valid date.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'name' => 123,
                    'birthdate' => 123,
                ],
                'expectedErrors' => [
                    'name' => 'The name field must be a string.',
                    'birthdate' => 'The birthdate field must be a valid date.',
                ],
            ],
            // Case 3: Birthdate is lower than 14 years
            [
                'data' => [
                    'name' => fake()->name(),
                    'birthdate' => today()->subYears(13)->toDateString(),
                ],
                'expectedErrors' => [
                    'birthdate' => 'You should have at least 14 years old to proceed.',
                ],
            ],
        ];
    }
}

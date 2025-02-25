<?php

namespace Tests\Unit\Requests\Admin\Cracker;

use Tests\Traits\HasDummyCracker;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Requests\Admin\Cracker\CrackerStoreRequest;

class CrackerStoreRequestTest extends BaseRequestTesting
{
    use RefreshDatabase;
    use HasDummyCracker;

    /**
     * Specify the request class to be tested.
     *
     * @return string
     */
    public function request(): string
    {
        return CrackerStoreRequest::class;
    }

    /**
     * Test if can validate the name field.
     *
     * @return void
     */
    public function test_if_can_validate_the_name_field(): void
    {
        $this->assertFalse($this->validate([
            'name' => '',
            'acting' => fake()->boolean(),
        ]));

        $this->assertFalse($this->validate([
            'name' => 123,
            'acting' => fake()->boolean(),
        ]));

        $cracker = $this->createDummyCracker();

        $this->assertFalse($this->validate([
            'name' => $cracker->name,
            'acting' => fake()->boolean(),
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->word(),
            'acting' => fake()->boolean(),
        ]));
    }

    /**
     * Test if can validate the acting field.
     *
     * @return void
     */
    public function test_if_can_validate_the_acting_field(): void
    {
        $this->assertFalse($this->validate([
            'name' => fake()->word(),
            'acting' => '',
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->word(),
            'acting' => '123',
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->word(),
            'acting' => 123,
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->word(),
            'acting' => fake()->randomElement([0, 1]),
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->word(),
            'acting' => fake()->randomElement(['0', '1']),
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->word(),
            'acting' => fake()->boolean(),
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
        if (isset($data['name']) && $data['name'] === $name = 'Duplicated') {
            $this->createDummyCracker([
                'name' => $name,
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
     * @return array<int, array{data: array{name: mixed, acting: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing fields
            [
                'data' => [
                    'name' => '',
                    'acting' => '',
                ],
                'expectedErrors' => [
                    'name' => 'The name field is required.',
                    'acting' => 'The acting field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'name' => 123,
                    'acting' => 123,
                ],
                'expectedErrors' => [
                    'name' => 'The name field must be a string.',
                    'acting' => 'The acting field must be true or false.',
                ],
            ],
            // Case 3: Duplicated case
            [
                'data' => [
                    'name' => 'Duplicated',
                    'acting' => fake()->boolean(),
                ],
                'expectedErrors' => [
                    'name' => 'The name has already been taken.',
                ],
            ],
        ];
    }
}

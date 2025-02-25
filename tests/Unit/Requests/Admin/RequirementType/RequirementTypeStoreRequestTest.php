<?php

namespace Tests\Unit\Requests\Admin\RequirementType;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Requests\Admin\RequirementType\RequirementTypeStoreRequest;

class RequirementTypeStoreRequestTest extends BaseRequestTesting
{
    use RefreshDatabase;

    /**
     * @inheritDoc
     */
    public function request(): string
    {
        return RequirementTypeStoreRequest::class;
    }

    /**
     * Test if can validate composite unique validation.
     *
     * @return void
     */
    public function test_composite_unique_validation(): void
    {
        $this->assertFalse($this->validate([
            'os' => '',
            'potential' => '',
        ]));

        $this->assertFalse($this->validate([
            'os' => 123,
            'potential' => 123,
        ]));

        $this->assertTrue($this->validate([
            'os' => fake()->randomElement(['windows', 'linux', 'mac']),
            'potential' => fake()->randomElement(['minimum', 'recommended', 'maximum']),
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
     * @return array<int, mixed>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing both fields.
            [
                'data' => [
                    'os' => '',
                    'potential' => '',
                ],
                'expectedErrors' => [
                    'os' => 'The os field is required.',
                    'potential' => 'The potential field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type.
            [
                'data' => [
                    'os' => 123,
                    'potential' => 123,
                ],
                'expectedErrors' => [
                    'os' => 'The os field must be a string.',
                    'potential' => 'The potential field must be a string.',
                ],
            ],
        ];
    }
}

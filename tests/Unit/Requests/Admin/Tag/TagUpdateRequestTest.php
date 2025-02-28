<?php

namespace Tests\Unit\Requests\Admin\Tag;

use Tests\Traits\HasDummyTag;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use App\Http\Requests\Admin\Tag\TagUpdateRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagUpdateRequestTest extends BaseRequestTesting
{
    use HasDummyTag;
    use RefreshDatabase;

    /**
     * Specify the request class to be tested.
     *
     * @return string
     */
    public function request(): string
    {
        return TagUpdateRequest::class;
    }

    /**
     * Test if can validate the q field.
     *
     * @return void
     */
    public function test_if_can_validate_the_q_field(): void
    {
        $this->assertFalse($this->validate([
            'name' => '',
        ]));

        $this->assertFalse($this->validate([
            'name' => 123,
        ]));

        $tag = $this->createDummyTag();

        $this->assertFalse($this->validate([
            'name' => $tag->name,
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->word(),
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
            $this->createDummyTag([
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
     * @return array<int, array{data: array{name: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing fields
            [
                'data' => [
                    'name' => '',
                ],
                'expectedErrors' => [
                    'name' => 'The name field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'name' => 123,
                ],
                'expectedErrors' => [
                    'name' => 'The name field must be a string.',
                ],
            ],
            // Case 3: Duplicated case
            [
                'data' => [
                    'name' => 'Duplicated',
                ],
                'expectedErrors' => [
                    'name' => 'The name has already been taken.',
                ],
            ],
        ];
    }
}

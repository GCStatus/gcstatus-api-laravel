<?php

namespace Tests\Unit\Requests\Admin\Languageable;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use App\Http\Requests\Admin\Languageable\LanguageableUpdateRequest;

class LanguageableUpdateRequestTest extends BaseRequestTesting
{
    /**
     * Specify the request class to be tested.
     *
     * @return string
     */
    public function request(): string
    {
        return LanguageableUpdateRequest::class;
    }

    /**
     * Test if can validate the menu field.
     *
     * @return void
     */
    public function test_if_can_validate_the_menu_field(): void
    {
        $this->assertFalse($this->validate([
            'menu' => '',
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
        ]));

        $this->assertFalse($this->validate([
            'menu' => 123,
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
        ]));

        $this->assertTrue($this->validate([
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
        ]));
    }

    /**
     * Test if can validate the dubs field.
     *
     * @return void
     */
    public function test_if_can_validate_the_dubs_field(): void
    {
        $this->assertFalse($this->validate([
            'menu' => fake()->boolean(),
            'dubs' => '',
            'subtitles' => fake()->boolean(),
        ]));

        $this->assertFalse($this->validate([
            'menu' => fake()->boolean(),
            'dubs' => 123,
            'subtitles' => fake()->boolean(),
        ]));

        $this->assertTrue($this->validate([
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
        ]));
    }

    /**
     * Test if can validate the subtitles field.
     *
     * @return void
     */
    public function test_if_can_validate_the_subtitles_field(): void
    {
        $this->assertFalse($this->validate([
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => '',
        ]));

        $this->assertFalse($this->validate([
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => 123,
        ]));

        $this->assertTrue($this->validate([
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
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
            // Case 1: Missing fields
            [
                'data' => [
                    'menu' => '',
                    'dubs' => '',
                    'subtitles' => '',
                ],
                'expectedErrors' => [
                    'menu' => 'The menu field is required.',
                    'dubs' => 'The dubs field is required.',
                    'subtitles' => 'The subtitles field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'menu' => 123,
                    'dubs' => 123,
                    'subtitles' => 123,
                ],
                'expectedErrors' => [
                    'menu' => 'The menu field must be true or false.',
                    'dubs' => 'The dubs field must be true or false.',
                    'subtitles' => 'The subtitles field must be true or false.',
                ],
            ],
        ];
    }
}

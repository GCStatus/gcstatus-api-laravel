<?php

namespace Tests\Unit\Requests\Admin\Languageable;

use Tests\Traits\HasDummyLanguage;
use App\Models\{Languageable, User};
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Requests\Admin\Languageable\LanguageableStoreRequest;

class LanguageableStoreRequestTest extends BaseRequestTesting
{
    use RefreshDatabase;
    use HasDummyLanguage;

    /**
     * Specify the request class to be tested.
     *
     * @return string
     */
    public function request(): string
    {
        return LanguageableStoreRequest::class;
    }

    /**
     * Test if can validate the languageable_id field.
     *
     * @return void
     */
    public function test_if_can_validate_the_languageable_id_field(): void
    {
        $this->assertFalse($this->validate([
            'languageable_id' => '',
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
            'language_id' => $this->createDummyLanguage()->id,
        ]));

        $this->assertFalse($this->validate([
            'languageable_id' => fake()->word(),
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
            'language_id' => $this->createDummyLanguage()->id,
        ]));

        $this->assertTrue($this->validate([
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
            'language_id' => $this->createDummyLanguage()->id,
        ]));
    }

    /**
     * Test if can validate the languageable_type field.
     *
     * @return void
     */
    public function test_if_can_validate_the_languageable_type_field(): void
    {
        $this->assertFalse($this->validate([
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => '',
            'language_id' => $this->createDummyLanguage()->id,
        ]));

        $this->assertFalse($this->validate([
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => 123,
            'language_id' => $this->createDummyLanguage()->id,
        ]));

        $this->assertFalse($this->validate([
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => fake()->word(),
            'language_id' => $this->createDummyLanguage()->id,
        ]));

        $this->assertTrue($this->validate([
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
            'language_id' => $this->createDummyLanguage()->id,
        ]));
    }

    /**
     * Test if can validate the menu field.
     *
     * @return void
     */
    public function test_if_can_validate_the_menu_field(): void
    {
        $this->assertFalse($this->validate([
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => '',
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
            'language_id' => $this->createDummyLanguage()->id,
        ]));

        $this->assertFalse($this->validate([
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => 123,
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
            'language_id' => $this->createDummyLanguage()->id,
        ]));

        $this->assertTrue($this->validate([
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
            'language_id' => $this->createDummyLanguage()->id,
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
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => fake()->boolean(),
            'dubs' => '',
            'subtitles' => fake()->boolean(),
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
            'language_id' => $this->createDummyLanguage()->id,
        ]));

        $this->assertFalse($this->validate([
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => fake()->boolean(),
            'dubs' => 123,
            'subtitles' => fake()->boolean(),
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
            'language_id' => $this->createDummyLanguage()->id,
        ]));

        $this->assertTrue($this->validate([
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
            'language_id' => $this->createDummyLanguage()->id,
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
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => '',
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
            'language_id' => $this->createDummyLanguage()->id,
        ]));

        $this->assertFalse($this->validate([
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => 123,
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
            'language_id' => $this->createDummyLanguage()->id,
        ]));

        $this->assertTrue($this->validate([
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
            'language_id' => $this->createDummyLanguage()->id,
        ]));
    }

    /**
     * Test if can validate the language_id field.
     *
     * @return void
     */
    public function test_if_can_validate_the_language_id_field(): void
    {
        $this->assertFalse($this->validate([
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
            'language_id' => '',
        ]));

        $this->assertFalse($this->validate([
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
            'language_id' => 123,
        ]));

        $this->assertFalse($this->validate([
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
            'language_id' => fake()->word(),
        ]));

        $this->assertTrue($this->validate([
            'languageable_id' => fake()->numberBetween(1, 999),
            'menu' => fake()->boolean(),
            'dubs' => fake()->boolean(),
            'subtitles' => fake()->boolean(),
            'languageable_type' => fake()->randomElement(Languageable::ALLOWED_LANGUAGEABLE_TYPES),
            'language_id' => $this->createDummyLanguage()->id,
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
                    'language_id' => '',
                    'languageable_id' => '',
                    'languageable_type' => '',
                ],
                'expectedErrors' => [
                    'menu' => 'The menu field is required.',
                    'dubs' => 'The dubs field is required.',
                    'subtitles' => 'The subtitles field is required.',
                    'language_id' => 'The language id field is required.',
                    'languageable_id' => 'The languageable id field is required.',
                    'languageable_type' => 'The languageable type field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'menu' => 123,
                    'dubs' => 123,
                    'subtitles' => 123,
                    'languageable_type' => 123,
                    'language_id' => fake()->word(),
                    'languageable_id' => fake()->word(),
                ],
                'expectedErrors' => [
                    'menu' => 'The menu field must be true or false.',
                    'dubs' => 'The dubs field must be true or false.',
                    'language_id' => 'The language id field must be a number.',
                    'subtitles' => 'The subtitles field must be true or false.',
                    'languageable_id' => 'The languageable id field must be a number.',
                    'languageable_type' => 'The languageable type field must be a string.',
                ],
            ],
            // Case 3: Invalid language_id
            [
                'data' => [
                    'language_id' => 999,
                ],
                'expectedErrors' => [
                    'language_id' => 'The selected language id is invalid.',
                ],
            ],
            // Case 4: Languageable type is not allowed
            [
                'data' => [
                    'languageable_type' => User::class,
                ],
                'expectedErrors' => [
                    'languageable_type' => 'The selected languageable type is invalid.',
                ],
            ],
        ];
    }
}

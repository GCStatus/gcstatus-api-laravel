<?php

namespace Tests\Unit\Requests\Admin\Galleriable;

use Illuminate\Http\UploadedFile;
use Tests\Traits\HasDummyMediaType;
use Database\Seeders\MediaTypeSeeder;
use App\Models\{MediaType, Galleriable, User};
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Requests\Admin\Galleriable\GalleriableStoreRequest;

class GalleriableStoreRequestTest extends BaseRequestTesting
{
    use RefreshDatabase;
    use HasDummyMediaType;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed([MediaTypeSeeder::class]);
    }

    /**
     * Specify the request class to be tested.
     *
     * @return string
     */
    public function request(): string
    {
        return GalleriableStoreRequest::class;
    }

    /**
     * Test if can validate the galleriable_id field.
     *
     * @return void
     */
    public function test_if_can_validate_the_galleriable_id_field(): void
    {
        $this->assertFalse($this->validate([
            'galleriable_id' => '',
            's3' => fake()->boolean(),
            'file' => UploadedFile::fake()->create('fake.png'),
            'url' => 'https://google.com',
            'media_type_id' => MediaType::PHOTO_CONST_ID,
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
        ]));

        $this->assertFalse($this->validate([
            'galleriable_id' => fake()->word(),
            's3' => fake()->boolean(),
            'file' => UploadedFile::fake()->create('fake.png'),
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'url' => 'https://google.com',
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));

        $this->assertTrue($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => fake()->boolean(),
            'file' => UploadedFile::fake()->create('fake.png'),
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'url' => 'https://google.com',
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));
    }

    /**
     * Test if can validate the s3 field.
     *
     * @return void
     */
    public function test_if_can_validate_the_s3_field(): void
    {
        $this->assertFalse($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => '',
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'file' => UploadedFile::fake()->create('fake.png'),
            'url' => 'https://google.com',
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));

        $this->assertFalse($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => 123,
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'file' => UploadedFile::fake()->create('fake.png'),
            'url' => 'https://google.com',
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));

        $this->assertTrue($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => fake()->boolean(),
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'file' => UploadedFile::fake()->create('fake.png'),
            'url' => 'https://google.com',
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));
    }

    /**
     * Test if can validate the galleriable_type field.
     *
     * @return void
     */
    public function test_if_can_validate_the_galleriable_type_field(): void
    {
        $this->assertFalse($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => fake()->boolean(),
            'galleriable_type' => '',
            'file' => UploadedFile::fake()->create('fake.png'),
            'url' => 'https://google.com',
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));

        $this->assertFalse($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => fake()->boolean(),
            'galleriable_type' => 123,
            'file' => UploadedFile::fake()->create('fake.png'),
            'url' => 'https://google.com',
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));

        $this->assertFalse($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => fake()->boolean(),
            'galleriable_type' => fake()->word(),
            'file' => UploadedFile::fake()->create('fake.png'),
            'url' => 'https://google.com',
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));

        $this->assertTrue($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => fake()->boolean(),
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'file' => UploadedFile::fake()->create('fake.png'),
            'url' => 'https://google.com',
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));
    }

    /**
     * Test if can validate the file field.
     *
     * @return void
     */
    public function test_if_can_validate_the_file_field(): void
    {
        $this->assertFalse($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => true,
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'url' => 'https://google.com',
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));

        $this->assertFalse($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => true,
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'file' => '',
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));

        $this->assertFalse($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => true,
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'file' => 123,
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));

        $this->assertFalse($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => true,
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'file' => fake()->word(),
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));

        $this->assertFalse($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => true,
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'file' => UploadedFile::fake()->create('fake.pdf'),
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));

        $this->assertTrue($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => true,
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'file' => UploadedFile::fake()->create('fake.png'),
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));

        $this->assertTrue($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => true,
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'file' => UploadedFile::fake()->create('fake.mp4'),
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));
    }

    /**
     * Test if can validate the url field.
     *
     * @return void
     */
    public function test_if_can_validate_the_url_field(): void
    {
        $this->assertFalse($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => false,
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'url' => '',
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));

        $this->assertFalse($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => false,
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'url' => 123,
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));

        $this->assertFalse($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => false,
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'url' => 'https://invalid.co',
            'media_type_id' => MediaType::PHOTO_CONST_ID,
        ]));

        $this->assertTrue($this->validate([
            'galleriable_id' => fake()->numberBetween(1, 999),
            's3' => false,
            'galleriable_type' => fake()->randomElement(Galleriable::ALLOWED_GALLERIABLES_TYPE),
            'url' => 'https://google.com',
            'media_type_id' => MediaType::PHOTO_CONST_ID,
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
                    's3' => '',
                    'galleriable_id' => '',
                    'galleriable_type' => '',
                ],
                'expectedErrors' => [
                    's3' => 'The s3 field is required.',
                    'galleriable_id' => 'The galleriable id field is required.',
                    'galleriable_type' => 'The galleriable type field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    's3' => 123,
                    'url' => 123,
                    'file' => 123,
                    'galleriable_type' => 123,
                    'galleriable_id' => 'invalid',
                ],
                'expectedErrors' => [
                    'url' => 'The url field must be a string.',
                    'file' => 'The file field must be a file.',
                    's3' => 'The s3 field must be true or false.',
                    'galleriable_id' => 'The galleriable id field must be a number.',
                    'galleriable_type' => 'The selected galleriable type is invalid.',
                ],
            ],
            // Case 3: Invalid url active url
            [
                'data' => [
                    's3' => false,
                    'url' => 'https://invalid.co',
                ],
                'expectedErrors' => [
                    'url' => 'The url field must be a valid URL.',
                ],
            ],
            // Case 4: Invalid file
            [
                'data' => [
                    's3' => true,
                    'file' => 'https://invalid.co',
                ],
                'expectedErrors' => [
                    'file' => 'The file field must be a file.',
                ],
            ],
            // Case 5: No s3 but url field is missing
            [
                'data' => [
                    's3' => false,
                    'url' => '',
                ],
                'expectedErrors' => [
                    'url' => 'The url field is required when s3 is false.',
                ],
            ],
            // Case 6: Is s3 but file field is missing
            [
                'data' => [
                    's3' => true,
                    'file' => '',
                ],
                'expectedErrors' => [
                    'file' => 'The file field is required when s3 is true.',
                ],
            ],
            // Case 7: Galleriable type is not allowed
            [
                'data' => [
                    'galleriable_type' => User::class,
                ],
                'expectedErrors' => [
                    'galleriable_type' => 'The selected galleriable type is invalid.',
                ],
            ],
            // Case 8: Invalid file type
            [
                'data' => [
                    's3' => true,
                    'file' => UploadedFile::fake()->create('fake.pdf')
                ],
                'expectedErrors' => [
                    'file' => 'The file field must be a file of type: png, jpg, jpeg, gif, bmp, webp, mp4, mov.',
                ],
            ],
            // Case 9: Invalid media type
            [
                'data' => [
                    'media_type_id' => 999,
                ],
                'expectedErrors' => [
                    'media_type_id' => 'The selected media type id is invalid.',
                ],
            ],
        ];
    }
}

<?php

namespace Tests\Unit\Requests\Profile;

use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use App\Http\Requests\Profile\PictureUpdateRequest;

class PictureUpdateRequestTest extends BaseRequestTesting
{
    /**
     * Specify the request class to be tested.
     *
     * @return string
     */
    public function request(): string
    {
        return PictureUpdateRequest::class;
    }

    /**
     * Test if can validate the file.
     *
     * @return void
     */
    public function test_if_can_validate_the_file(): void
    {
        $this->assertFalse($this->validate([
            'file' => '',
        ]));

        $this->assertFalse($this->validate([
            'file' => 123,
        ]));

        $this->assertFalse($this->validate([
            'file' => UploadedFile::fake()->create('invalid.pdf'),
        ]));

        $this->assertFalse($this->validate([
            'file' => UploadedFile::fake()->create('valid.png', 3000),
        ]));

        $this->assertTrue($this->validate([
            'file' => UploadedFile::fake()->create('fake.png'),
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
     * @return array<int, array{data: array{file: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing fields
            [
                'data' => [
                    'file' => '',
                ],
                'expectedErrors' => [
                    'file' => 'The file field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'file' => 123,
                ],
                'expectedErrors' => [
                    'file' => 'The file field must be a file.',
                ],
            ],
            // Case 3: Invalid mime type
            [
                'data' => [
                    'file' => UploadedFile::fake()->create('fake.pdf'),
                ],
                'expectedErrors' => [
                    'file' => 'The file field must be a file of type: jpg, bmp, png, jpeg, gif.',
                ],
            ],
            // Case 4: Invalid file size (higher than allowed)
            [
                'data' => [
                    'file' => UploadedFile::fake()->create('valid.png', 3000),
                ],
                'expectedErrors' => [
                    'file' => 'The file field must not be greater than 2048 kilobytes.',
                ],
            ],
        ];
    }
}

<?php

namespace Tests\Unit\Requests\Admin\TorrentProvider;

use Tests\Traits\HasDummyTorrentProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Requests\Admin\TorrentProvider\TorrentProviderStoreRequest;

class TorrentProviderStoreRequestTest extends BaseRequestTesting
{
    use RefreshDatabase;
    use HasDummyTorrentProvider;

    /**
     * @inheritDoc
     */
    public function request(): string
    {
        return TorrentProviderStoreRequest::class;
    }

    /**
     * Test if can validate the name.
     *
     * @return void
     */
    public function test_if_can_validate_the_name(): void
    {
        $this->assertFalse($this->validate([
            'name' => '',
            'url' => 'https://google.com',
        ]));

        $this->assertFalse($this->validate([
            'name' => 123,
            'url' => 'https://google.com',
        ]));

        $torrentProvider = $this->createDummyTorrentProvider();

        $this->assertFalse($this->validate([
            'name' => $torrentProvider->name,
            'url' => 'https://google.com',
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->word(),
            'url' => 'https://google.com',
        ]));
    }

    /**
     * Test if can validate the url.
     *
     * @return void
     */
    public function test_if_can_validate_the_url(): void
    {
        $this->assertFalse($this->validate([
            'name' => fake()->word(),
            'url' => '',
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->word(),
            'url' => 123,
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->word(),
            'url' => 'https://invalid.com',
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->word(),
            'url' => 'https://google.com',
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
            $this->createDummyTorrentProvider([
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
     * @return array<int, mixed>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing fields
            [
                'data' => [
                    'url' => '',
                    'name' => '',
                ],
                'expectedErrors' => [
                    'url' => 'The url field is required.',
                    'name' => 'The name field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'url' => 123,
                    'name' => 123,
                ],
                'expectedErrors' => [
                    'url' => 'The url field must be a string.',
                    'name' => 'The name field must be a string.',
                ],
            ],
            // Case 3: Duplicated case
            [
                'data' => [
                    'name' => 'Duplicated',
                    'url' => 'https://google.com',
                ],
                'expectedErrors' => [
                    'name' => 'The name has already been taken.',
                ],
            ],
            // Case 4: Invalid url dns
            [
                'data' => [
                    'name' => fake()->word(),
                    'url' => 'https://invalid.com',
                ],
                'expectedErrors' => [
                    'url' => 'The url field must be a valid URL.',
                ],
            ],
        ];
    }
}

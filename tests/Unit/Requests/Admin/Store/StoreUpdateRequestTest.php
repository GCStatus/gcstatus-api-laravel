<?php

namespace Tests\Unit\Requests\Admin\Store;

use Tests\Traits\HasDummyStore;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Requests\Admin\Store\StoreUpdateRequest;

class StoreUpdateRequestTest extends BaseRequestTesting
{
    use HasDummyStore;
    use RefreshDatabase;

    /**
     * @inheritDoc
     */
    public function request(): string
    {
        return StoreUpdateRequest::class;
    }

    /**
     * Test if can validate the name.
     *
     * @return void
     */
    public function test_if_can_validate_the_name(): void
    {
        $this->assertFalse($this->validate([
            'name' => 123,
            'url' => 'https://google.com',
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
        ]));

        $store = $this->createDummyStore();

        $this->assertFalse($this->validate([
            'name' => $store->name,
            'url' => 'https://google.com',
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
        ]));

        $this->assertTrue($this->validate([
            'name' => '',
            'url' => 'https://google.com',
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->word(),
            'url' => 'https://google.com',
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
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
            'url' => 123,
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->word(),
            'url' => 'https://invalid.com',
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
        ]));


        $this->assertTrue($this->validate([
            'name' => fake()->word(),
            'url' => '',
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->word(),
            'url' => 'https://google.com',
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
        ]));
    }

    /**
     * Test if can validate the logo.
     *
     * @return void
     */
    public function test_if_can_validate_the_logo(): void
    {
        $this->assertFalse($this->validate([
            'name' => fake()->word(),
            'url' => 'https://google.com',
            'logo' => 123,
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->word(),
            'url' => 'https://google.com',
            'logo' => 'https://invalid.com',
        ]));


        $this->assertTrue($this->validate([
            'name' => fake()->word(),
            'url' => 'https://google.com',
            'logo' => '',
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->word(),
            'url' => 'https://google.com',
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
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
            $this->createDummyStore([
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
     * @return array<int, array{data: array{name: mixed, url: mixed, logo: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Fields are the wrong type
            [
                'data' => [
                    'url' => 123,
                    'name' => 123,
                    'logo' => 123,
                ],
                'expectedErrors' => [
                    'url' => 'The url field must be a string.',
                    'name' => 'The name field must be a string.',
                    'logo' => 'The logo field must be a string.',
                ],
            ],
            // Case 2: Duplicated case
            [
                'data' => [
                    'name' => 'Duplicated',
                    'url' => 'https://google.com',
                    'logo' => 'https://placehold.co/600x400/EEE/31343C',
                ],
                'expectedErrors' => [
                    'name' => 'The name has already been taken.',
                ],
            ],
            // Case 3: Invalid url dns
            [
                'data' => [
                    'name' => fake()->word(),
                    'url' => 'https://invalid.com',
                    'logo' => 'https://placehold.co/600x400/EEE/31343C',
                ],
                'expectedErrors' => [
                    'url' => 'The url field must be a valid URL.',
                ],
            ],
            // Case 4: Invalid logo dns
            [
                'data' => [
                    'name' => fake()->word(),
                    'url' => 'https://google.com',
                    'logo' => 'https://invalid',
                ],
                'expectedErrors' => [
                    'logo' => 'The logo field must be a valid URL.',
                ],
            ],
        ];
    }
}

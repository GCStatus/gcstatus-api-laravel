<?php

namespace Tests\Unit\Requests\Admin\Critic;

use Tests\Traits\HasDummyCritic;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Requests\Admin\Critic\CriticStoreRequest;

class CriticStoreRequestTest extends BaseRequestTesting
{
    use HasDummyCritic;
    use RefreshDatabase;

    /**
     * Specify the request class to be tested.
     *
     * @return string
     */
    public function request(): string
    {
        return CriticStoreRequest::class;
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
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
            'url' => 'https://google.com',
        ]));

        $this->assertFalse($this->validate([
            'name' => 123,
            'acting' => fake()->boolean(),
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
            'url' => 'https://google.com',
        ]));

        $Critic = $this->createDummyCritic();

        $this->assertFalse($this->validate([
            'name' => $Critic->name,
            'acting' => fake()->boolean(),
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
            'url' => 'https://google.com',
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->word(),
            'acting' => fake()->boolean(),
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
            'url' => 'https://google.com',
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
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
            'url' => 'https://google.com',
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->word(),
            'acting' => '123',
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
            'url' => 'https://google.com',
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->word(),
            'acting' => 123,
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
            'url' => 'https://google.com',
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->word(),
            'acting' => fake()->randomElement([0, 1]),
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
            'url' => 'https://google.com',
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->word(),
            'acting' => fake()->randomElement(['0', '1']),
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
            'url' => 'https://google.com',
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->word(),
            'acting' => fake()->boolean(),
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
            'url' => 'https://google.com',
        ]));
    }

    /**
     * Test if can validate the logo field.
     *
     * @return void
     */
    public function test_if_can_validate_the_logo_field(): void
    {
        $this->assertFalse($this->validate([
            'name' => fake()->word(),
            'acting' => fake()->boolean(),
            'logo' => '',
            'url' => 'https://google.com',
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->word(),
            'acting' => fake()->boolean(),
            'logo' => 123,
            'url' => 'https://google.com',
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->word(),
            'acting' => fake()->boolean(),
            'logo' => 'https://invalid.c',
            'url' => 'https://google.com',
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->word(),
            'acting' => fake()->boolean(),
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
            'url' => 'https://google.com',
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
            'name' => fake()->word(),
            'acting' => fake()->boolean(),
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
            'url' => '',
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->word(),
            'acting' => fake()->boolean(),
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
            'url' => 123,
        ]));

        $this->assertFalse($this->validate([
            'name' => fake()->word(),
            'acting' => fake()->boolean(),
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
            'url' => 'https://invalid.c',
        ]));

        $this->assertTrue($this->validate([
            'name' => fake()->word(),
            'acting' => fake()->boolean(),
            'logo' => 'https://placehold.co/600x400/EEE/31343C',
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
            $this->createDummyCritic([
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
                    'logo' => '',
                    'acting' => '',
                ],
                'expectedErrors' => [
                    'url' => 'The url field is required.',
                    'name' => 'The name field is required.',
                    'logo' => 'The logo field is required.',
                    'acting' => 'The acting field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'url' => 123,
                    'name' => 123,
                    'logo' => 123,
                    'acting' => 123,
                ],
                'expectedErrors' => [
                    'url' => 'The url field must be a string.',
                    'name' => 'The name field must be a string.',
                    'logo' => 'The logo field must be a string.',
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
            // Case 4: invalid url
            [
                'data' => [
                    'url' => 'https://invalid.c',
                ],
                'expectedErrors' => [
                    'url' => 'The url field must be a valid URL.',
                ],
            ],
            // Case 5: invalid logo
            [
                'data' => [
                    'logo' => 'https://invalid.c',
                ],
                'expectedErrors' => [
                    'logo' => 'The logo field must be a valid URL.',
                ],
            ],
        ];
    }
}

<?php

namespace Tests\Unit\Requests\Admin\Dlc;

use Tests\Traits\{HasDummyDlc, HasDummyGame};
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use App\Http\Requests\Admin\Dlc\DlcStoreRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DlcStoreRequestTest extends BaseRequestTesting
{
    use HasDummyDlc;
    use HasDummyGame;
    use RefreshDatabase;

    /**
     * Specify the request class to be tested.
     *
     * @return string
     */
    public function request(): string
    {
        return DlcStoreRequest::class;
    }

    /**
     * Test if can validate the title field.
     *
     * @return void
     */
    public function test_if_can_validate_the_title_field(): void
    {
        $game = $this->createDummyGame();

        $this->assertFalse($this->validate([
            'title' => '',
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertFalse($this->validate([
            'title' => 123,
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $dlc = $this->createDummyDlc();

        $this->assertFalse($this->validate([
            'title' => $dlc->title,
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));
    }

    /**
     * Test if can validate the free field.
     *
     * @return void
     */
    public function test_if_can_validate_the_free_field(): void
    {
        $game = $this->createDummyGame();

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => '',
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => 123,
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));
    }

    /**
     * Test if can validate the legal field.
     *
     * @return void
     */
    public function test_if_can_validate_the_legal_field(): void
    {
        $game = $this->createDummyGame();

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => 123,
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => '',
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));
    }

    /**
     * Test if can validate the about field.
     *
     * @return void
     */
    public function test_if_can_validate_the_about_field(): void
    {
        $game = $this->createDummyGame();

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => '',
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => 123,
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => 'short about',
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));
    }

    /**
     * Test if can validate the cover field.
     *
     * @return void
     */
    public function test_if_can_validate_the_cover_field(): void
    {
        $game = $this->createDummyGame();

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => '',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 123,
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://invalid.co',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));
    }

    /**
     * Test if can validate the release_date field.
     *
     * @return void
     */
    public function test_if_can_validate_the_release_date_field(): void
    {
        $game = $this->createDummyGame();

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => '',
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => 123,
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => '123',
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));
    }

    /**
     * Test if can validate the description field.
     *
     * @return void
     */
    public function test_if_can_validate_the_description_field(): void
    {
        $game = $this->createDummyGame();

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => 123,
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => 'short description',
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => '',
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));
    }

    /**
     * Test if can validate the short_description field.
     *
     * @return void
     */
    public function test_if_can_validate_the_short_description_field(): void
    {
        $game = $this->createDummyGame();

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => '',
            'game_id' => $game->id,
        ]));

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => 123,
            'game_id' => $game->id,
        ]));

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => 'short',
            'game_id' => $game->id,
        ]));

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
        ]));
    }

    /**
     * Test if can validate the game_id field.
     *
     * @return void
     */
    public function test_if_can_validate_the__game_id_field(): void
    {
        $game = $this->createDummyGame();

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => '',
        ]));

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => 99999,
        ]));

        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => 'abc',
        ]));

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'game_id' => $game->id,
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
        if (isset($data['title']) && $data['title'] === $title = 'Duplicated') {
            $this->createDummyDlc([
                'title' => $title,
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
     * @return array<int, array{data: array{title: mixed, free: mixed, legal: mixed, about: mixed, description: mixed, short_description: mixed, release_date: mixed, game_id: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing fields
            [
                'data' => [
                    'title' => '',
                    'free' => '',
                    'legal' => '',
                    'about' => '',
                    'cover' => '',
                    'release_date' => '',
                    'description' => '',
                    'short_description' => '',
                    'game_id' => '',
                ],
                'expectedErrors' => [
                    'title' => 'The title field is required.',
                    'free' => 'The free field is required.',
                    'about' => 'The about field is required.',
                    'cover' => 'The cover field is required.',
                    'release_date' => 'The release date field is required.',
                    'short_description' => 'The short description field is required.',
                    'game_id' => 'The game id field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'title' => 123,
                    'free' => 123,
                    'legal' => 123,
                    'about' => 123,
                    'cover' => 123,
                    'release_date' => 123,
                    'description' => 123,
                    'short_description' => 123,
                    'game_id' => 'abc',
                ],
                'expectedErrors' => [
                    'title' => 'The title field must be a string.',
                    'free' => 'The free field must be true or false.',
                    'about' => 'The about field must be a string.',
                    'cover' => 'The cover field must be a string.',
                    'release_date' => 'The release date field must be a string.',
                    'short_description' => 'The short description field must be a string.',
                    'game_id' => 'The game id field must be a number.',
                ],
            ],
            // Case 3: Duplicated case
            [
                'data' => [
                    'title' => 'Duplicated',
                    'free' => fake()->boolean(),
                    'legal' => fake()->text(),
                    'about' => fake()->realText(),
                    'cover' => 'https://placehold.co/600x400/EEE/31343C',
                    'release_date' => fake()->date(),
                    'description' => fake()->realText(),
                    'short_description' => fake()->realText(),
                    'game_id' => 1,
                ],
                'expectedErrors' => [
                    'title' => 'The title has already been taken.',
                ],
            ],
            // Case 4: Invalid cover active url
            [
                'data' => [
                    'title' => fake()->title(),
                    'free' => fake()->boolean(),
                    'legal' => fake()->text(),
                    'about' => fake()->realText(),
                    'cover' => 'https://invalid.co',
                    'release_date' => fake()->date(),
                    'description' => fake()->realText(),
                    'short_description' => fake()->realText(),
                    'game_id' => 1,
                ],
                'expectedErrors' => [
                    'cover' => 'The cover field must be a valid URL.',
                ],
            ],
            // Case 5: Game id is invalid
            [
                'data' => [
                    'title' => fake()->title(),
                    'free' => fake()->boolean(),
                    'legal' => fake()->text(),
                    'about' => fake()->realText(),
                    'cover' => 'https://placehold.co/600x400/EEE/31343C',
                    'release_date' => fake()->date(),
                    'description' => fake()->realText(),
                    'short_description' => fake()->realText(),
                    'game_id' => 1,
                ],
                'expectedErrors' => [
                    'game_id' => 'The selected game id is invalid.',
                ],
            ],
        ];
    }
}

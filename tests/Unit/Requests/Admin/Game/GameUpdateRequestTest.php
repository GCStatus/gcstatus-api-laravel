<?php

namespace Tests\Unit\Requests\Admin\Game;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use App\Http\Requests\Admin\Game\GameUpdateRequest;
use Database\Seeders\StatusSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\Traits\{
    HasDummyGame,
    HasDummyTag,
    HasDummyGenre,
    HasDummyStatus,
    HasDummyCracker,
    HasDummyCategory,
    HasDummyPlatform,
    HasDummyPublisher,
    HasDummyDeveloper,
    HasDummyProtection,
};

class GameUpdateRequestTest extends BaseRequestTesting
{
    use HasDummyGame;
    use HasDummyTag;
    use HasDummyGenre;
    use HasDummyStatus;
    use HasDummyCracker;
    use RefreshDatabase;
    use HasDummyPlatform;
    use HasDummyCategory;
    use HasDummyPublisher;
    use HasDummyDeveloper;
    use HasDummyProtection;

    /**
     * Specify the request class to be tested.
     *
     * @return string
     */
    public function request(): string
    {
        return GameUpdateRequest::class;
    }

    /**
     * Test if can validate the title field.
     *
     * @return void
     */
    public function test_if_can_validate_the_title_field(): void
    {
        $this->assertFalse($this->validate([
            'title' => 123,
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));

        $game = $this->createDummyGame();

        $this->assertFalse($this->validate([
            'title' => $game->title,
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));

        $this->assertTrue($this->validate([
            'title' => '',
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
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
        ]));
    }

    /**
     * Test if can validate the free field.
     *
     * @return void
     */
    public function test_if_can_validate_the_free_field(): void
    {
        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => 123,
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => '',
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
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
        ]));
    }

    /**
     * Test if can validate the legal field.
     *
     * @return void
     */
    public function test_if_can_validate_the_legal_field(): void
    {
        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => 123,
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
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
        ]));
    }

    /**
     * Test if can validate the about field.
     *
     * @return void
     */
    public function test_if_can_validate_the_about_field(): void
    {
        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => 123,
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
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
        ]));

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => '',
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
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
        ]));
    }

    /**
     * Test if can validate the cover field.
     *
     * @return void
     */
    public function test_if_can_validate_the_cover_field(): void
    {
        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 123,
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
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
        ]));

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => '',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
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
        ]));
    }

    /**
     * Test if can validate the release_date field.
     *
     * @return void
     */
    public function test_if_can_validate_the_release_date_field(): void
    {
        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => 123,
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
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
        ]));

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => '',
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
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
        ]));
    }

    /**
     * Test if can validate the description field.
     *
     * @return void
     */
    public function test_if_can_validate_the_description_field(): void
    {
        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => 123,
            'short_description' => fake()->realText(),
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
        ]));
    }

    /**
     * Test if can validate the short_description field.
     *
     * @return void
     */
    public function test_if_can_validate_the_short_description_field(): void
    {
        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => 123,
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
        ]));

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => '',
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
        ]));
    }

    /**
     * Test if can validate the age field.
     *
     * @return void
     */
    public function test_if_can_validate_the_age_field(): void
    {
        $this->assertFalse($this->validate([
            'age' => 'abc',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));

        $this->assertTrue($this->validate([
            'age' => '',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));

        $this->assertFalse($this->validate([
            'age' => -1,
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));

        $this->assertFalse($this->validate([
            'age' => 19,
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));

        $this->assertTrue($this->validate([
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));
    }

    /**
     * Test if can validate the condition field.
     *
     * @return void
     */
    public function test_if_can_validate_the_condition_field(): void
    {
        $this->assertFalse($this->validate([
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => 123,
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));

        $this->assertFalse($this->validate([
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => 'invalid',
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));

        $this->assertTrue($this->validate([
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => '',
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));

        $this->assertTrue($this->validate([
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));
    }

    /**
     * Test if can validate the great_release field.
     *
     * @return void
     */
    public function test_if_can_validate_the_great_release_field(): void
    {
        $this->assertFalse($this->validate([
            'age' => '0',
            'great_release' => 123,
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));

        $this->assertFalse($this->validate([
            'age' => '0',
            'great_release' => 'abc',
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));

        $this->assertTrue($this->validate([
            'age' => '0',
            'great_release' => '',
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));

        $this->assertTrue($this->validate([
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));
    }

    /**
     * Test if can validate the website field.
     *
     * @return void
     */
    public function test_if_can_validate_the_website_field(): void
    {
        $this->assertFalse($this->validate([
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'website' => 123,
        ]));

        $this->assertFalse($this->validate([
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'website' => 'https://invalid.co',
        ]));

        $this->assertTrue($this->validate([
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'website' => '',
        ]));

        $this->assertTrue($this->validate([
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'website' => 'https://google.com',
        ]));
    }

    /**
     * Test if can validate the tags field.
     *
     * @return void
     */
    public function test_if_can_validate_the_tags_field(): void
    {
        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'tags' => [
                1
            ],
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
            'tags' => [],
        ]));

        $tag = $this->createDummyTag();

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'tags' => [
                $tag->id,
            ],
        ]));
    }

    /**
     * Test if can validate the genres field.
     *
     * @return void
     */
    public function test_if_can_validate_the_genres_field(): void
    {
        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'genres' => [
                1
            ],
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
            'genres' => [],
        ]));

        $genre = $this->createDummyGenre();

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'genres' => [
                $genre->id,
            ],
        ]));
    }

    /**
     * Test if can validate the platforms field.
     *
     * @return void
     */
    public function test_if_can_validate_the_platforms_field(): void
    {
        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'platforms' => [
                1
            ],
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
            'platforms' => [],
        ]));

        $platform = $this->createDummyPlatform();

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'platforms' => [
                $platform->id,
            ],
        ]));
    }

    /**
     * Test if can validate the categories field.
     *
     * @return void
     */
    public function test_if_can_validate_the_categories_field(): void
    {
        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'categories' => [
                1
            ],
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
            'categories' => [],
        ]));

        $category = $this->createDummyCategory();

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'categories' => [
                $category->id,
            ],
        ]));
    }

    /**
     * Test if can validate the publishers field.
     *
     * @return void
     */
    public function test_if_can_validate_the_publishers_field(): void
    {
        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'publishers' => [
                1
            ],
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
            'publishers' => [],
        ]));

        $publisher = $this->createDummyPublisher();

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'publishers' => [
                $publisher->id,
            ],
        ]));
    }

    /**
     * Test if can validate the developers field.
     *
     * @return void
     */
    public function test_if_can_validate_the_developers_field(): void
    {
        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'developers' => [
                1
            ],
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
            'developers' => [],
        ]));

        $developer = $this->createDummyDeveloper();

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'developers' => [
                $developer->id,
            ],
        ]));
    }

    /**
     * Test if can validate the crack field.
     *
     * @return void
     */
    public function test_if_can_validate_the_crack_field(): void
    {
        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'crack' => [
                'cracker_id' => 1,
            ],
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
            'crack' => [
                'protection_id' => 1,
            ],
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
            'crack' => [
                'status' => 1,
            ],
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
            'crack' => [
                'status' => 'invalid',
            ],
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
            'crack' => [
                'cracked_at' => 123,
            ],
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
            'crack' => [
                'cracked_at' => Carbon::tomorrow()->toDateString(),
            ],
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
            'crack' => [],
        ]));

        $this->seed([StatusSeeder::class]);

        $cracker = $this->createDummyCracker();
        $protection = $this->createDummyProtection();

        $this->assertTrue($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'crack' => [
                'cracker_id' => $cracker->id,
                'protection_id' => $protection->id,
                'cracked_at' => Carbon::yesterday()->toDateString(),
                'status' => fake()->randomElement(['cracked', 'uncracked', 'cracked-oneday']),
            ],
        ]));
    }

    /**
     * Test if can validate the support field.
     *
     * @return void
     */
    public function test_if_can_validate_the_support_field(): void
    {
        $this->assertFalse($this->validate([
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
            'support' => [
                'url' => 1,
                'email' => 1,
                'contact' => 1,
            ],
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
            'support' => [
                'email' => 'https://invalid.co',
            ],
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
            'support' => [
                'email' => 'invalidemail',
            ],
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
            'support' => [],
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
            'support' => [
                'email' => 'valid@gmail.com',
                'url' => 'https://google.com',
                'contact' => fake()->phoneNumber(),
            ],
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
            $this->createDummyGame([
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
     * @return array<int, mixed>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Fields are the wrong type
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
                    'tags' => ['abc'],
                    'genres' => ['abc'],
                    'platforms' => ['abc'],
                    'categories' => ['abc'],
                    'publishers' => ['abc'],
                    'developers' => ['abc'],
                    'crack' => 132,
                ],
                'expectedErrors' => [
                    'title' => 'The title field must be a string.',
                    'free' => 'The free field must be true or false.',
                    'about' => 'The about field must be a string.',
                    'cover' => 'The cover field must be a string.',
                    'release_date' => 'The release date field must be a string.',
                    'short_description' => 'The short description field must be a string.',
                    'crack' => 'The crack field must be an array.',
                    'tags.0' => 'The tags.0 field must be a number.',
                    'genres.0' => 'The genres.0 field must be a number.',
                    'platforms.0' => 'The platforms.0 field must be a number.',
                    'categories.0' => 'The categories.0 field must be a number.',
                    'publishers.0' => 'The publishers.0 field must be a number.',
                    'developers.0' => 'The developers.0 field must be a number.',
                ],
            ],
            // Case 2: Duplicated case
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
                ],
                'expectedErrors' => [
                    'title' => 'The title has already been taken.',
                ],
            ],
            // Case 3: Invalid cover active url
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
                ],
                'expectedErrors' => [
                    'cover' => 'The cover field must be a valid URL.',
                ],
            ],
            // Case 4: Invalid classifications and issuers
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
                    'tags' => [1],
                    'genres' => [1],
                    'platforms' => [2],
                    'categories' => [4],
                    'publishers' => [99],
                    'developers' => [85],
                ],
                'expectedErrors' => [
                    'tags.0' => 'The selected tags.0 is invalid.',
                    'genres.0' => 'The selected genres.0 is invalid.',
                    'platforms.0' => 'The selected platforms.0 is invalid.',
                    'categories.0' => 'The selected categories.0 is invalid.',
                    'publishers.0' => 'The selected publishers.0 is invalid.',
                    'developers.0' => 'The selected developers.0 is invalid.',
                ],
            ],
            // Case 5: Invalid website
            [
                'data' => [
                    'website' => 'https://invalid.co',
                ],
                'expectedErrors' => [
                    'website' => 'The website field must be a valid URL.',
                ],
            ],
            // Case 6: Invalid condition
            [
                'data' => [
                    'condition' => 'invalid',
                ],
                'expectedErrors' => [
                    'condition' => 'The selected condition is invalid.',
                ],
            ],
            // Case 7: Invalid cracker id
            [
                'data' => [
                    'crack' => [
                        'cracker_id' => 1,
                    ],
                ],
                'expectedErrors' => [
                    'crack.cracker_id' => 'The selected crack.cracker id is invalid.',
                ],
            ],
            // Case 8: Invalid cracker id
            [
                'data' => [
                    'crack' => [
                        'protection_id' => 1,
                    ],
                ],
                'expectedErrors' => [
                    'crack.protection_id' => 'The selected crack.protection id is invalid.',
                ],
            ],
            // Case 8: Invalid status
            [
                'data' => [
                    'crack' => [
                        'status' => 'invalid',
                    ],
                ],
                'expectedErrors' => [
                    'crack.status' => 'The selected crack.status is invalid.',
                ],
            ],
            // Case 8: Invalid cracked_at
            [
                'data' => [
                    'crack' => [
                        'cracked_at' => Carbon::tomorrow()->toDateString(),
                    ],
                ],
                'expectedErrors' => [
                    'crack.cracked_at' => 'The crack.cracked at field must be a date before or equal to today.',
                ],
            ],
            // Case 9: Invalid email
            [
                'data' => [
                    'support' => [
                        'email' => 'invalidmail',
                    ],
                ],
                'expectedErrors' => [
                    'support.email' => 'The support.email field must be a valid email address.',
                ],
            ],
            // Case 10: Invalid support url
            [
                'data' => [
                    'support' => [
                        'url' => 'https://invalid.co',
                    ],
                ],
                'expectedErrors' => [
                    'support.url' => 'The support.url field must be a valid URL.',
                ],
            ],
        ];
    }
}

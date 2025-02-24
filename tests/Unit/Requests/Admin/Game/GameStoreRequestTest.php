<?php

namespace Tests\Unit\Requests\Admin\Game;

use Tests\Traits\HasDummyGame;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use App\Http\Requests\Admin\Game\GameStoreRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GameStoreRequestTest extends BaseRequestTesting
{
    use HasDummyGame;
    use RefreshDatabase;

    /**
     * Specify the request class to be tested.
     *
     * @return string
     */
    public function request(): string
    {
        return GameStoreRequest::class;
    }

    /**
     * Test if can validate the title field.
     *
     * @return void
     */
    public function test_if_can_validate_the_title_field(): void
    {
        $this->assertFalse($this->validate([
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => '',
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
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
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
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
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
     * Test if can validate the free field.
     *
     * @return void
     */
    public function test_if_can_validate_the_free_field(): void
    {
        $this->assertFalse($this->validate([
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => fake()->word(),
            'free' => '',
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
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
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
     * Test if can validate the legal field.
     *
     * @return void
     */
    public function test_if_can_validate_the_legal_field(): void
    {
        $this->assertFalse($this->validate([
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
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
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
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
     * Test if can validate the about field.
     *
     * @return void
     */
    public function test_if_can_validate_the_about_field(): void
    {
        $this->assertFalse($this->validate([
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => '',
            'cover' => 'https://placehold.co/600x400/EEE/31343C',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));

        $this->assertFalse($this->validate([
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
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
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
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
     * Test if can validate the cover field.
     *
     * @return void
     */
    public function test_if_can_validate_the_cover_field(): void
    {
        $this->assertFalse($this->validate([
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
            'title' => fake()->word(),
            'free' => fake()->boolean(),
            'legal' => fake()->text(),
            'about' => fake()->realText(),
            'cover' => '',
            'release_date' => fake()->date(),
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
        ]));

        $this->assertFalse($this->validate([
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
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
            'age' => '0',
            'great_release' => fake()->boolean(),
            'condition' => fake()->randomElement(['common', 'hot', 'popular', 'unreleased', 'sale']),
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
     * Test if can validate the release_date field.
     *
     * @return void
     */
    public function test_if_can_validate_the_release_date_field(): void
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
            'release_date' => '',
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
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
            'release_date' => 123,
            'description' => fake()->realText(),
            'short_description' => fake()->realText(),
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
            'release_date' => '123',
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
     * Test if can validate the description field.
     *
     * @return void
     */
    public function test_if_can_validate_the_description_field(): void
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
            'description' => 123,
            'short_description' => fake()->realText(),
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
            'description' => 'short description',
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
            'description' => '',
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
     * Test if can validate the short_description field.
     *
     * @return void
     */
    public function test_if_can_validate_the_short_description_field(): void
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
            'short_description' => '',
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
            'short_description' => 123,
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
            'short_description' => 'short',
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
     * Test if can validate the age field.
     *
     * @return void
     */
    public function test_if_can_validate_the_age_field(): void
    {
        $this->assertFalse($this->validate([
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
            // Case 1: Missing fields
            [
                'data' => [
                    'age' => '',
                    'condition' => '',
                    'great_release' => '',
                    'title' => '',
                    'free' => '',
                    'legal' => '',
                    'about' => '',
                    'cover' => '',
                    'release_date' => '',
                    'description' => '',
                    'short_description' => '',
                ],
                'expectedErrors' => [
                    'age' => 'The age field is required.',
                    'condition' => 'The condition field is required.',
                    'great_release' => 'The great release field is required.',
                    'title' => 'The title field is required.',
                    'free' => 'The free field is required.',
                    'about' => 'The about field is required.',
                    'cover' => 'The cover field is required.',
                    'release_date' => 'The release date field is required.',
                    'short_description' => 'The short description field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'age' => 'kas',
                    'condition' => 123,
                    'great_release' => 'kas',
                    'title' => 123,
                    'free' => 123,
                    'legal' => 123,
                    'about' => 123,
                    'cover' => 123,
                    'release_date' => 123,
                    'description' => 123,
                    'short_description' => 123,
                ],
                'expectedErrors' => [
                    'age' => 'The age field must be a number.',
                    'condition' => 'The condition field must be a string.',
                    'great_release' => 'The great release field must be true or false.',
                    'title' => 'The title field must be a string.',
                    'free' => 'The free field must be true or false.',
                    'about' => 'The about field must be a string.',
                    'cover' => 'The cover field must be a string.',
                    'release_date' => 'The release date field must be a string.',
                    'short_description' => 'The short description field must be a string.',
                ],
            ],
            // Case 3: Duplicated case
            [
                'data' => [
                    'title' => 'Duplicated',
                ],
                'expectedErrors' => [
                    'title' => 'The title has already been taken.',
                ],
            ],
            // Case 4: Invalid cover active url
            [
                'data' => [
                    'cover' => 'https://invalid.co',
                ],
                'expectedErrors' => [
                    'cover' => 'The cover field must be a valid URL.',
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
        ];
    }
}

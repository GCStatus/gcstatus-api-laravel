<?php

namespace Tests\Unit\Requests\Profile;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Contracts\Requests\BaseRequestTesting;
use App\Http\Requests\Profile\SocialUpdateRequest;

class SocialUpdateRequestTest extends BaseRequestTesting
{
    /**
     * A dummy valid url.
     *
     * @var string
     */
    private const VALID_URL = 'https://google.com';

    /**
     * Specify the request class to be tested.
     *
     * @return string
     */
    public function request(): string
    {
        return SocialUpdateRequest::class;
    }

    /**
     * Test if can validate the share.
     *
     * @return void
     */
    public function test_if_can_validate_the_share(): void
    {
        $this->assertFalse($this->validate([
            'share' => '',
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));

        $this->assertFalse($this->validate([
            'share' => 123,
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));

        $this->assertTrue($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));
    }

    /**
     * Test if can validate the phone.
     *
     * @return void
     */
    public function test_if_can_validate_the_phone(): void
    {
        $this->assertFalse($this->validate([
            'share' => fake()->boolean(),
            'phone' => 123,
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));

        $this->assertTrue($this->validate([
            'share' => fake()->boolean(),
            'phone' => '',
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));

        $this->assertTrue($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));
    }

    /**
     * Test if can validate the twitch.
     *
     * @return void
     */
    public function test_if_can_validate_the_twitch(): void
    {
        $this->assertFalse($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => 123,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));

        $this->assertTrue($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => '',
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));

        $this->assertTrue($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));
    }

    /**
     * Test if can validate the github.
     *
     * @return void
     */
    public function test_if_can_validate_the_github(): void
    {
        $this->assertFalse($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => 123,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));

        $this->assertTrue($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => '',
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));

        $this->assertTrue($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));
    }

    /**
     * Test if can validate the twitter.
     *
     * @return void
     */
    public function test_if_can_validate_the_twitter(): void
    {
        $this->assertFalse($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => 123,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));

        $this->assertTrue($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => '',
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));

        $this->assertTrue($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));
    }

    /**
     * Test if can validate the youtube.
     *
     * @return void
     */
    public function test_if_can_validate_the_youtube(): void
    {
        $this->assertFalse($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => 123,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));

        $this->assertTrue($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => '',
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));

        $this->assertTrue($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));
    }

    /**
     * Test if can validate the facebook.
     *
     * @return void
     */
    public function test_if_can_validate_the_facebook(): void
    {
        $this->assertFalse($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => 123,
            'instagram' => self::VALID_URL,
        ]));

        $this->assertTrue($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => '',
            'instagram' => self::VALID_URL,
        ]));

        $this->assertTrue($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
        ]));
    }

    /**
     * Test if can validate the instagram.
     *
     * @return void
     */
    public function test_if_can_validate_the_instagram(): void
    {
        $this->assertFalse($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => 123,
        ]));

        $this->assertTrue($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => '',
        ]));

        $this->assertTrue($this->validate([
            'share' => fake()->boolean(),
            'phone' => fake()->phoneNumber(),
            'twitch' => self::VALID_URL,
            'github' => self::VALID_URL,
            'twitter' => self::VALID_URL,
            'youtube' => self::VALID_URL,
            'facebook' => self::VALID_URL,
            'instagram' => self::VALID_URL,
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
     * @return array<int, array{data: array{share: mixed, phone: mixed, twitch: mixed, github: mixed, twitter: mixed, youtube: mixed, facebook: mixed, instagram: mixed}, expectedErrors: array<string, string>}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            // Case 1: Missing fields
            [
                'data' => [
                    'share' => '',
                    'phone' => '',
                    'twitch' => '',
                    'github' => '',
                    'twitter' => '',
                    'youtube' => '',
                    'facebook' => '',
                    'instagram' => '',
                ],
                'expectedErrors' => [
                    'share' => 'The share field is required.',
                ],
            ],
            // Case 2: Fields are the wrong type
            [
                'data' => [
                    'share' => 123,
                    'phone' => 123,
                    'twitch' => 123,
                    'github' => 123,
                    'twitter' => 123,
                    'youtube' => 123,
                    'facebook' => 123,
                    'instagram' => 123,
                ],
                'expectedErrors' => [
                    'share' => 'The share field must be true or false.',
                    'phone' => 'The phone field must be a string.',
                    'twitch' => 'The twitch field must be a string.',
                    'github' => 'The github field must be a string.',
                    'twitter' => 'The twitter field must be a string.',
                    'youtube' => 'The youtube field must be a string.',
                    'facebook' => 'The facebook field must be a string.',
                    'instagram' => 'The instagram field must be a string.',
                ],
            ],
            // Case 3: Invalid active urls
            [
                'data' => [
                    'share' => fake()->boolean(),
                    'phone' => fake()->phoneNumber(),
                    'twitch' => 'invalid.com',
                    'github' => 'invalid.com',
                    'twitter' => 'invalid.com',
                    'youtube' => 'invalid.com',
                    'facebook' => 'invalid.com',
                    'instagram' => 'invalid.com',
                ],
                'expectedErrors' => [
                    'twitch' => 'The twitch field must be a valid URL.',
                    'github' => 'The github field must be a valid URL.',
                    'twitter' => 'The twitter field must be a valid URL.',
                    'youtube' => 'The youtube field must be a valid URL.',
                    'facebook' => 'The facebook field must be a valid URL.',
                    'instagram' => 'The instagram field must be a valid URL.',
                ],
            ],
        ];
    }
}

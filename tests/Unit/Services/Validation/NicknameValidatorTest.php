<?php

namespace Tests\Unit\Services\Validation;

use Tests\TestCase;
use App\Contracts\Services\Validation\IdentifierValidatorInterface;

class NicknameValidatorTest extends TestCase
{
    /**
     * The nickname validator instance.
     *
     * @var \App\Contracts\Services\Validation\IdentifierValidatorInterface
     */
    private IdentifierValidatorInterface $nicknameValidator;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->nicknameValidator = $this->app->make(IdentifierValidatorInterface::class, ['type' => 'nickname']);
    }

    /**
     * Test that valid nickname pass validation.
     *
     * @return void
     */
    public function test_valid_nickname_passes_validation(): void
    {
        $validNickname = fake()->userName();

        $this->assertTrue($this->nicknameValidator->validate($validNickname));
    }

    /**
     * Test that invalid nickname fail validation.
     *
     * @return void
     */
    public function test_invalid_nickname_fails_validation(): void
    {
        $invalidNickname = 'invalid-nick name';

        $this->assertFalse($this->nicknameValidator->validate($invalidNickname));
    }
}

<?php

namespace Tests\Unit\Services\Validation;

use Tests\TestCase;
use App\Contracts\Services\Validation\IdentifierValidatorInterface;

class EmailValidatorTest extends TestCase
{
    /**
     * The email validator instance.
     *
     * @var \App\Contracts\Services\Validation\IdentifierValidatorInterface
     */
    private IdentifierValidatorInterface $emailValidator;

    /**
     * Set up the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->emailValidator = $this->app->make(IdentifierValidatorInterface::class, ['type' => 'email']);
    }

    /**
     * Test that valid email addresses pass validation.
     *
     * @return void
     */
    public function test_valid_email_passes_validation(): void
    {
        $validEmail = 'example@gmail.com';

        $this->assertTrue($this->emailValidator->validate($validEmail));
    }

    /**
     * Test that invalid email addresses fail validation.
     *
     * @return void
     */
    public function test_invalid_email_fails_validation(): void
    {
        $invalidEmail = 'invalid-email';

        $this->assertFalse($this->emailValidator->validate($invalidEmail));
    }

    /**
     * Test that an email without a valid domain fails validation.
     *
     * @return void
     */
    public function test_invalid_domain_email_fails_validation(): void
    {
        $invalidDomainEmail = 'example@invalid-domain';

        $this->assertFalse($this->emailValidator->validate($invalidDomainEmail));
    }
}

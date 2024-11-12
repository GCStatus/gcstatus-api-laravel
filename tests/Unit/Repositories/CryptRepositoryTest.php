<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Contracts\Repositories\CryptRepositoryInterface;

class CryptRepositoryTest extends TestCase
{
    /**
     * The abstract repository.
     *
     * @var \App\Contracts\Repositories\CryptRepositoryInterface
     */
    private CryptRepositoryInterface $repository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(CryptRepositoryInterface::class);
    }

    /**
     * Test if can encrypt a value and get crypted string on return.
     *
     * @return void
     */
    public function test_if_can_encrypt_a_value_and_get_crypted_string_on_return(): void
    {
        $toCrypt = fake()->word();

        $crypted = $this->repository->encrypt($toCrypt);

        $this->assertNotEquals($toCrypt, $crypted);
    }

    /**
     * Test if can decrypt a value and get the original value on return.
     *
     * @return void
     */
    public function test_if_can_decrypt_a_value_and_get_the_original_value_on_return(): void
    {
        $toCrypt = fake()->word();

        $crypted = $this->repository->encrypt($toCrypt);

        $this->assertNotEquals($toCrypt, $crypted);

        $decrypted = $this->repository->decrypt($crypted);

        $this->assertEquals($decrypted, $toCrypt);
    }
}

<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Services\CryptService;
use App\Contracts\Services\CryptServiceInterface;
use App\Contracts\Repositories\CryptRepositoryInterface;

class CryptServiceTest extends TestCase
{
    /**
     * The abstract service.
     *
     * @var \App\Contracts\Services\CryptServiceInterface
     */
    private CryptServiceInterface $cryptService;

    /**
     * The crypt repository mock interface.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $mockRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mockRepository = Mockery::mock(CryptRepositoryInterface::class);

        /** @var \App\Contracts\Repositories\CryptRepositoryInterface $mockRepository */
        $mockRepository = $this->mockRepository;
        $this->cryptService = new CryptService($mockRepository);
    }

    /**
     * Test if can encrypt a value and get crypted string on return.
     *
     * @return void
     */
    public function test_if_can_encrypt_a_value_and_get_crypted_string_on_return(): void
    {
        $toCrypt = fake()->word();

        $this->mockRepository
            ->shouldReceive('encrypt')
            ->once()
            ->with($toCrypt)
            ->andReturn('cryptedValue');

        $crypted = $this->cryptService->encrypt($toCrypt);

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

        $this->mockRepository
            ->shouldReceive('encrypt')
            ->once()
            ->with($toCrypt)
            ->andReturn(fake()->word());

        $crypted = $this->cryptService->encrypt($toCrypt);

        $this->assertNotEquals($toCrypt, $crypted);

        $this->mockRepository
            ->shouldReceive('decrypt')
            ->once()
            ->with($crypted)
            ->andReturn($toCrypt);

        $decrypted = $this->cryptService->decrypt($crypted);

        $this->assertEquals($decrypted, $toCrypt);
    }
}

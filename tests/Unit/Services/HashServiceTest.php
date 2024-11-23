<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use App\Services\HashService;
use App\Contracts\Services\HashServiceInterface;
use App\Contracts\Repositories\HashRepositoryInterface;

class HashServiceTest extends TestCase
{
    /**
     * The hash repository.
     *
     * @var \Mockery\MockInterface
     */
    private $hashRepository;

    /**
     * The hash service.
     *
     * @var \App\Contracts\Services\HashServiceInterface
     */
    private HashServiceInterface $hashService;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->hashRepository = Mockery::mock(HashRepositoryInterface::class);

        /** @var \App\Contracts\Repositories\HashRepositoryInterface $hashRepository */
        $hashRepository = $this->hashRepository;

        $this->hashService = new HashService($hashRepository);
    }

    /**
     * Test if can hash a given value.
     *
     * @return void
     */
    public function test_if_can_hash_a_given_value(): void
    {
        $hashable = 'randomstring123';
        $hash = 'hashforrandomstring123';

        $this->hashRepository
            ->shouldReceive('make')
            ->once()
            ->with($hashable)
            ->andReturn($hash);

        $this->hashRepository
            ->shouldReceive('check')
            ->once()
            ->with($hash, $hashable)
            ->andReturnTrue();

        $this->hashService->make($hashable);

        $this->assertNotEquals($hashable, $hash);
        $this->assertTrue($this->hashService->check($hash, $hashable));
    }

    /**
     * Test if can check a given hash.
     *
     * @return void
     */
    public function test_if_can_check_a_given_hash(): void
    {
        $hashable = 'randomstring123';
        $hash = 'hashforrandomstring123';

        $this->hashRepository
            ->shouldReceive('check')
            ->once()
            ->with($hash, $hashable)
            ->andReturnTrue();

        $this->assertTrue($this->hashService->check($hash, $hashable));
    }
}

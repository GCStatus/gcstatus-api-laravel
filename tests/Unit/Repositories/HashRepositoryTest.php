<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Contracts\Repositories\HashRepositoryInterface;

class HashRepositoryTest extends TestCase
{
    /**
     * The hash repository.
     *
     * @var \App\Contracts\Repositories\HashRepositoryInterface
     */
    private HashRepositoryInterface $hashRepository;

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->hashRepository = app(HashRepositoryInterface::class);
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

        Hash::shouldReceive('make')
            ->once()
            ->with($hashable)
            ->andReturn($hash);

        Hash::shouldReceive('check')
            ->once()
            ->with($hashable, $hash)
            ->andReturnTrue();

        $this->hashRepository->make($hashable);

        $this->assertNotEquals($hashable, $hash);
        $this->assertTrue($this->hashRepository->check($hash, $hashable));
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

        Hash::shouldReceive('check')
            ->once()
            ->with($hashable, $hash)
            ->andReturnTrue();

        $this->assertTrue($this->hashRepository->check($hash, $hashable));
    }
}

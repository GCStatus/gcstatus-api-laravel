<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use App\Contracts\Repositories\CacheRepositoryInterface;

class CacheRepositoryTest extends TestCase
{
    /**
     * The abstract repository.
     *
     * @var \App\Contracts\Repositories\CacheRepositoryInterface
     */
    private CacheRepositoryInterface $repository;

    /**
     * The testable cache key.
     *
     * @var string
     */
    private const CACHE_KEY = 'fake';

    /**
     * Setup new test environments.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(CacheRepositoryInterface::class);
    }

    /**
     * Test if can put a cache on store with valid payload.
     *
     * @return void
     */
    public function test_if_can_put_a_cache_on_store_with_valid_payload(): void
    {
        $key = self::CACHE_KEY;
        $cacheable = fake()->word();

        Cache::shouldReceive('put')
            ->once()
            ->with($key, $cacheable, 60)
            ->andReturnTrue();

        $result = $this->repository->put($key, $cacheable, 60);

        $this->assertTrue($result);
    }

    /**
     * Test if can remove (forget) a cache.
     *
     * @return void
     */
    public function test_if_can_forget_cache(): void
    {
        $key = self::CACHE_KEY;

        Cache::shouldReceive('forget')
            ->once()
            ->with($key)
            ->andReturnTrue();

        $result = $this->repository->forget($key);

        $this->assertTrue($result);
    }

    /**
     * Test if can check existence of a cache.
     *
     * @return void
     */
    public function test_if_can_check_cache_existence(): void
    {
        $key = self::CACHE_KEY;

        Cache::shouldReceive('has')
            ->once()
            ->with($key)
            ->andReturnTrue();

        $result = $this->repository->has($key);

        $this->assertTrue($result);
    }

    /**
     * Test if can retrieve a cache's value.
     *
     * @return void
     */
    public function test_if_can_get_cache_value(): void
    {
        $key = self::CACHE_KEY;
        $cacheable = fake()->word();

        Cache::shouldReceive('get')
            ->once()
            ->with($key)
            ->andReturn($cacheable);

        $this->assertEquals($cacheable, $this->repository->get($key));
    }

    /**
     * Test if can retrieve and remove cache on pull.
     *
     * @return void
     */
    public function test_if_can_retrieve_and_remove_cache_on_pull(): void
    {
        $key = self::CACHE_KEY;
        $cacheable = fake()->word();

        Cache::shouldReceive('pull')
            ->once()
            ->with($key)
            ->andReturn($cacheable);

        $result = $this->repository->pull($key);

        $this->assertEquals($cacheable, $result);

        Cache::shouldReceive('get')
            ->with($key)
            ->andReturn(null);

        $this->assertNull($this->repository->get($key));
    }
}

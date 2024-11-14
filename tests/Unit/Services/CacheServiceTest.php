<?php

namespace Tests\Unit\Services;

use Mockery;
use Tests\TestCase;
use Mockery\MockInterface;
use App\Services\CacheService;
use App\Contracts\Services\CacheServiceInterface;
use App\Contracts\Repositories\CacheRepositoryInterface;

class CacheServiceTest extends TestCase
{
    /**
     * The abstract service.
     *
     * @var \App\Contracts\Services\CacheServiceInterface
     */
    private CacheServiceInterface $cacheService;

    /**
     * The cookie repository mock interface.
     *
     * @var \Mockery\MockInterface
     */
    private MockInterface $mockRepository;

    /**
     * The testable cookie key.
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

        $this->mockRepository = Mockery::mock(CacheRepositoryInterface::class);

        /** @var \App\Contracts\Repositories\CacheRepositoryInterface $mockRepository */
        $mockRepository = $this->mockRepository;
        $this->cacheService = new CacheService($mockRepository);
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

        $this->mockRepository
            ->shouldReceive('put')
            ->once()
            ->with($key, $cacheable, 60)
            ->andReturnTrue();

        $result = $this->cacheService->put($key, $cacheable, 60);

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

        $this->mockRepository
            ->shouldReceive('forget')
            ->once()
            ->with($key);

        $this->cacheService->forget($key);
    }

    /**
     * Test if can check existence of a cache.
     *
     * @return void
     */
    public function test_if_can_check_cache_existence(): void
    {
        $key = self::CACHE_KEY;

        $this->mockRepository
            ->shouldReceive('has')
            ->once()
            ->with($key)
            ->andReturnTrue();

        $result = $this->cacheService->has($key);

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
        $value = 'test_value';

        $this->mockRepository
            ->shouldReceive('get')
            ->once()
            ->with($key)
            ->andReturn($value);

        $this->assertEquals($value, $this->cacheService->get($key));
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

        $this->mockRepository
            ->shouldReceive('pull')
            ->once()
            ->with($key)
            ->andReturn($cacheable);

        $result = $this->cacheService->pull($key);

        $this->assertEquals($cacheable, $result);

        $this->mockRepository
            ->shouldReceive('get')
            ->with($key)
            ->andReturn(null);

        $this->assertNull($this->cacheService->get($key));
    }
}

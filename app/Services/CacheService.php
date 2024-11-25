<?php

namespace App\Services;

use DateInterval;
use DateTimeInterface;
use App\Contracts\Services\CacheServiceInterface;
use App\Contracts\Repositories\CacheRepositoryInterface;

class CacheService implements CacheServiceInterface
{
    /**
     * The cache repository.
     *
     * @var \App\Contracts\Repositories\CacheRepositoryInterface
     */
    private $cacheRepository;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Repositories\CacheRepositoryInterface $cacheRepository
     * @return void
     */
    public function __construct(CacheRepositoryInterface $cacheRepository)
    {
        $this->cacheRepository = $cacheRepository;
    }

    /**
     * Put any value on cache.
     *
     * @param string $key
     * @param mixed $value
     * @param DateTimeInterface|DateInterval|int|null $ttl
     * @return bool
     */
    public function put(string $key, mixed $value, DateTimeInterface|DateInterval|int|null $ttl): bool
    {
        return $this->cacheRepository->put($key, $value, $ttl);
    }

    /**
     * Put a value on cache forever (until manual removal).
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function forever(string $key, mixed $value): bool
    {
        return $this->cacheRepository->forever($key, $value);
    }

    /**
     * Check if has any cache with given key.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->cacheRepository->has($key);
    }

    /**
     * Retrieve given cache value by key.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return $this->cacheRepository->get($key);
    }

    /**
     * Pull the item from cache (retrieve it values and delete it).
     *
     * @param string $key
     * @return mixed
     */
    public function pull(string $key): mixed
    {
        return $this->cacheRepository->pull($key);
    }

    /**
     * Forget a stored cache by key.
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        return $this->cacheRepository->forget($key);
    }
}

<?php

namespace App\Repositories;

use DateInterval;
use DateTimeInterface;
use Illuminate\Support\Facades\Cache;
use App\Contracts\Repositories\CacheRepositoryInterface;

class CacheRepository implements CacheRepositoryInterface
{
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
        return Cache::put($key, $value, $ttl);
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
        return Cache::forever($key, $value);
    }

    /**
     * Check if has any cache with given key.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return Cache::has($key);
    }

    /**
     * Retrieve given cache value by key.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return Cache::get($key);
    }

    /**
     * Pull the item from cache (retrieve it values and delete it).
     *
     * @param string $key
     * @return mixed
     */
    public function pull(string $key): mixed
    {
        return Cache::pull($key);
    }

    /**
     * Forget a stored cache by key.
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        return Cache::forget($key);
    }
}

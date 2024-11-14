<?php

namespace App\Contracts\Services;

use DateInterval;
use DateTimeInterface;

interface CacheServiceInterface
{
    /**
     * Put any value on cache.
     *
     * @param string $key
     * @param mixed $value
     * @param DateTimeInterface|DateInterval|int|null $ttl
     * @return bool
     */
    public function put(string $key, mixed $value, DateTimeInterface|DateInterval|int|null $ttl): bool;

    /**
     * Check if has any cache with given key.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Retrieve given cache value by key.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed;

    /**
     * Pull the item from cache (retrieve it values and delete it).
     *
     * @param string $key
     * @return mixed
     */
    public function pull(string $key): mixed;

    /**
     * Forget a stored cache by key.
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool;
}

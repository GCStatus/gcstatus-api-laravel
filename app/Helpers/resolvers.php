<?php

use App\Contracts\Services\{
    CacheServiceInterface,
    StorageServiceInterface,
};

if (!function_exists('storage')) {
    /**
     * Bind the storage service to helper function.
     *
     * @return \App\Contracts\Services\StorageServiceInterface
     */
    function storage(): StorageServiceInterface
    {
        return resolve(StorageServiceInterface::class);
    }
}

if (!function_exists('cacher')) {
    /**
     * Bind the cache service to helper function.
     *
     * @return \App\Contracts\Services\CacheServiceInterface
     */
    function cacher(): CacheServiceInterface
    {
        return resolve(CacheServiceInterface::class);
    }
}

<?php

use App\Contracts\Services\{
    CacheServiceInterface,
    LogServiceInterface,
    StorageServiceInterface,
    ProgressCalculatorServiceInterface,
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

if (!function_exists('progressCalculator')) {
    /**
     * Bind the progress calculator service to helper function.
     *
     * @return \App\Contracts\Services\ProgressCalculatorServiceInterface
     */
    function progressCalculator(): ProgressCalculatorServiceInterface
    {
        return resolve(ProgressCalculatorServiceInterface::class);
    }
}

if (!function_exists('logService')) {
    /**
     * Bind the log service to helper function.
     *
     * @return \App\Contracts\Services\LogServiceInterface
     */
    function logService(): LogServiceInterface
    {
        return resolve(LogServiceInterface::class);
    }
}

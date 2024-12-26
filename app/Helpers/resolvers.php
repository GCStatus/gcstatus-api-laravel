<?php

use App\Contracts\Services\{
    LogServiceInterface,
    AwardServiceInterface,
    CacheServiceInterface,
    StorageServiceInterface,
    TransactionServiceInterface,
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

if (!function_exists('transactionService')) {
    /**
     * Bind the instance of transaction service to helper function.
     *
     * @return \App\Contracts\Services\TransactionServiceInterface
     */
    function transactionService(): TransactionServiceInterface
    {
        return resolve(TransactionServiceInterface::class);
    }
}

if (!function_exists('awarder')) {
    /**
     * Bind the instance of award service to a helper function.
     *
     * @return \App\Contracts\Services\AwardServiceInterface
     */
    function awarder(): AwardServiceInterface
    {
        return resolve(AwardServiceInterface::class);
    }
}

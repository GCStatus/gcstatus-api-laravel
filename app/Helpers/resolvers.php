<?php

use App\Contracts\Services\StorageServiceInterface;

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

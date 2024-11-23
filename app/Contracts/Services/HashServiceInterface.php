<?php

namespace App\Contracts\Services;

interface HashServiceInterface
{
    /**
     * Create a hash of a given value.
     *
     * @param string $hashable
     * @return string
     */
    public function make(string $hashable): string;

    /**
     * Check if hash match with given value.
     *
     * @param string $hash
     * @param string $value
     * @return bool
     */
    public function check(string $hash, string $value): bool;
}

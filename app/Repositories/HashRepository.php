<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Hash;
use App\Contracts\Repositories\HashRepositoryInterface;

class HashRepository implements HashRepositoryInterface
{
    /**
     * Create a hash of a given value.
     *
     * @param string $hashable
     * @return string
     */
    public function make(string $hashable): string
    {
        return Hash::make($hashable);
    }

    /**
     * Check if hash match with given value.
     *
     * @param string $hash
     * @param string $value
     * @return bool
     */
    public function check(string $hash, string $value): bool
    {
        return Hash::check($value, $hash);
    }
}

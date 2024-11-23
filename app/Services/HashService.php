<?php

namespace App\Services;

use App\Contracts\Services\HashServiceInterface;
use App\Contracts\Repositories\HashRepositoryInterface;

class HashService implements HashServiceInterface
{
    /**
     * The hash repository.
     *
     * @var \App\Contracts\Repositories\HashRepositoryInterface
     */
    private HashRepositoryInterface $hashRepository;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Repositories\HashRepositoryInterface $hashRepository
     * @return void
     */
    public function __construct(HashRepositoryInterface $hashRepository)
    {
        $this->hashRepository = $hashRepository;
    }

    /**
     * Create a hash of a given value.
     *
     * @param string $hashable
     * @return string
     */
    public function make(string $hashable): string
    {
        return $this->hashRepository->make($hashable);
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
        return $this->hashRepository->check($hash, $value);
    }
}

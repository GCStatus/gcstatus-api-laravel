<?php

namespace App\Services;

use App\Contracts\Services\CryptServiceInterface;
use App\Contracts\Repositories\CryptRepositoryInterface;

class CryptService implements CryptServiceInterface
{
    /**
     * The cookie repository.
     *
     * @var \App\Contracts\Repositories\CryptRepositoryInterface
     */
    private $cryptRepository;

    /**
     * Create a new class instance.
     *
     * @param \App\Contracts\Repositories\CryptRepositoryInterface $cryptRepository
     * @return void
     */
    public function __construct(CryptRepositoryInterface $cryptRepository)
    {
        $this->cryptRepository = $cryptRepository;
    }

    /**
     * Crypt and return the given value.
     *
     * @param mixed $toCrypt
     * @return string
     */
    public function encrypt(mixed $toCrypt): string
    {
        return $this->cryptRepository->encrypt($toCrypt);
    }

    /**
     * Decrypt and return the given value.
     *
     * @param string $toDecrypt
     * @return mixed
     */
    public function decrypt(string $toDecrypt): mixed
    {
        return $this->cryptRepository->decrypt($toDecrypt);
    }
}

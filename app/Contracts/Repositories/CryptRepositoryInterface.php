<?php

namespace App\Contracts\Repositories;

interface CryptRepositoryInterface
{
    /**
     * Crypt and return the given value.
     *
     * @param mixed $toCrypt
     * @return string
     */
    public function encrypt(mixed $toCrypt): string;

    /**
     * Decrypt and return the given value.
     *
     * @param string $toDecrypt
     * @return mixed
     */
    public function decrypt(string $toDecrypt): mixed;
}

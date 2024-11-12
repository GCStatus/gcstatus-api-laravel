<?php

namespace App\Contracts\Services;

interface CryptServiceInterface
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

<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Crypt;
use App\Contracts\Repositories\CryptRepositoryInterface;

class CryptRepository implements CryptRepositoryInterface
{
    /**
     * Crypt and return the given value.
     *
     * @param mixed $toCrypt
     * @return string
     */
    public function encrypt(mixed $toCrypt): string
    {
        return Crypt::encrypt($toCrypt);
    }

    /**
     * Decrypt and return the given value.
     *
     * @param string $toDecrypt
     * @return mixed
     */
    public function decrypt(string $toDecrypt): mixed
    {
        return Crypt::decrypt($toDecrypt);
    }
}

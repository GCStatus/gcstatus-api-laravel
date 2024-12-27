<?php

namespace App\Exceptions\Wallet;

use App\Exceptions\BadRequestException;

class WalletHasntBalanceEnoughException extends BadRequestException
{
    /**
     * The response messge.
     *
     * @var string
     */
    protected $message = 'Your wallet has no balance enough for this operation!';
}

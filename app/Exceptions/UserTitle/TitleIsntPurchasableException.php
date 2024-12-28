<?php

namespace App\Exceptions\UserTitle;

use App\Exceptions\BadRequestException;

class TitleIsntPurchasableException extends BadRequestException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'The given title is not purchasable!';
}

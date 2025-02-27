<?php

namespace App\Exceptions\Admin\Languageable;

use App\Exceptions\ConflictException;

class LanguageableAlreadyExistsException extends ConflictException
{
    /**
     * The response message.
     *
     * @var string
     */
    protected $message = 'The given language already exists for this languageable!';
}

<?php

namespace Spatie\LaravelPasskeys\Exceptions;

use Exception;
use Throwable;

class InvalidPasskeyOptions extends Exception
{
    public static function invalidJson(): self
    {
        return new static('The given passkey options should be formatted as json. Please check the format and try again.');
    }
}

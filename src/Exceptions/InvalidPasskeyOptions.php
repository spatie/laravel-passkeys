<?php

namespace Spatie\LaravelPasskeys\Exceptions;

use Exception;

class InvalidPasskeyOptions extends Exception
{
    public static function invalidJson(): self
    {
        return new self('The given passkey options should be formatted as json. Please check the format and try again.');
    }
}

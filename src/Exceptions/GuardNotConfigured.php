<?php

namespace Spatie\LaravelPasskeys\Exceptions;

use Exception;

class GuardNotConfigured extends Exception
{
    public static function make(string $guard): self
    {
        return new self("The guard `{$guard}` is not configured in the `passkeys.php` config file.");
    }
}

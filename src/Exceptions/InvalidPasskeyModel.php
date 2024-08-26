<?php

namespace Spatie\LaravelPasskeys\Exceptions;

use Exception;
use Spatie\LaravelPasskeys\Models\Passkey;

class InvalidPasskeyModel extends Exception
{
    public static function make(string $configuredClass): self
    {
        $shouldExtend = Passkey::class;

        return new static("The configured passkey model `{$configuredClass}` does not extend `{$shouldExtend}`.");
    }
}

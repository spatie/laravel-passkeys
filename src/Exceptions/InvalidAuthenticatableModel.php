<?php

namespace Spatie\LaravelPasskeys\Exceptions;

use Exception;

class InvalidAuthenticatableModel extends Exception
{
    public static function traitMissing(string $modelClass, string $traitFqcn): self
    {
        return new static("The model `{$modelClass}` does not use the `{$traitFqcn}}` trait.");
    }
}

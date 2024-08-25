<?php

namespace Spatie\LaravelPasskeys\Exceptions;

use Exception;
use Throwable;

class InvalidPasskey extends Exception
{
    public static function invalidJson(): self
    {
        return new self('The given passkey should be formatted as json. Please check the format and try again.');
    }

    public static function invalidPublicKeyCredential(): self
    {
        return new self('The given passkey is not a valid public key credential. Please check the format and try again.');
    }

    public static function invalidAuthenticatorAttestationResponse(Throwable $exception): self
    {
        return new self(
            'The given passkey could not be validated. Please check the format and try again.',
            previous: $exception,
        );
    }
}

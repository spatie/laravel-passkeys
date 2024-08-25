<?php

namespace Spatie\LaravelPasskeys\Events;

use Spatie\LaravelPasskeys\Models\Passkey;

class PasskeyUsedToAuthenticateEvent
{
    public function __construct(
        public Passkey $passkey,
    ) {}
}

<?php

namespace Spatie\LaravelPasskeys\Events;

use Spatie\LaravelPasskeys\Http\Requests\AuthenticateUsingPasskeysRequest;
use Spatie\LaravelPasskeys\Models\Passkey;

class PasskeyCreatedEvent
{
    public function __construct(
        public Passkey $passkey,
    ) {}
}

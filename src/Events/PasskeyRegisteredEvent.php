<?php

namespace Spatie\LaravelPasskeys\Events;

use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Passkey;

class PasskeyRegisteredEvent
{
    public function __construct(
        public Passkey $passkey,
        public HasPasskeys $authenticatable,
    ) {}
}

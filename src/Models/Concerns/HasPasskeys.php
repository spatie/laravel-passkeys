<?php

namespace Spatie\LaravelPasskeys\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface HasPasskeys
{
    public function passkeys(): HasMany;

    public function getPassKeyName(): string;

    public function getPassKeyId(): string;

    public function getPassKeyDisplayName(): string;
}

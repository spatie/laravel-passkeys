<?php

namespace Spatie\LaravelPasskeys\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Spatie\LaravelPasskeys\Models\Passkey;

/**
 * @mixin Model
 *
 * @property Collection<Passkey> $passkeys
 */
interface HasPasskeys
{
    public function passkeys(): HasMany;

    public function getPassKeyName(): string;

    public function getPassKeyId(): string;

    public function getPassKeyDisplayName(): string;
}

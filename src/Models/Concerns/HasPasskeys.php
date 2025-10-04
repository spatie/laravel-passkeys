<?php

namespace Spatie\LaravelPasskeys\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @property \Illuminate\Support\Collection<\Spatie\LaravelPasskeys\Models\Passkey> $passkeys
 */
interface HasPasskeys
{
    /**
     * @return HasMany|MorphMany
     */
    public function passkeys();

    public function getPassKeyName(): string;

    public function getPassKeyId(): string;

    public function getPassKeyDisplayName(): string;
}

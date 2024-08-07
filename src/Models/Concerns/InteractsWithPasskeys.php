<?php

namespace Spatie\LaravelPasskeys\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\LaravelPasskeys\Support\Config;

trait InteractsWithPasskeys
{
    public function passkeys(): HasMany
    {
        $passkeyModel = Config::getPassKeyModel();

        return $this->hasMany($passkeyModel, 'authenticatable_id');
    }

    public function getPasskeyName(): string
    {
        return $this->email;
    }

    public function getPasskeyId(): string
    {
        return $this->id;
    }

    public function getPasskeyDisplayName(): string
    {
        return $this->name;
    }
}

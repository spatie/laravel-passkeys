<?php

namespace Spatie\LaravelPasskeys\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\LaravelPasskeys\Support\Config;

trait InteractsWithPasskeys
{
    /**
     * @return HasMany|MorphMany
     */
    public function passkeys()
    {
        $passkeyModel = Config::getPasskeyModel();

        if (! Config::isMultiAuthEnabled()) {
            return $this->hasMany($passkeyModel, 'authenticatable_id');
        }

        return $this->morphMany($passkeyModel, 'authenticatable');
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

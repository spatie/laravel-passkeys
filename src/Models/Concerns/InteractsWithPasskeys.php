<?php

namespace Spatie\LaravelPasskeys\Models\Concerns;

trait InteractsWithPasskeys
{
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

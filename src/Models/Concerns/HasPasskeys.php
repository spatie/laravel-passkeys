<?php

namespace Spatie\LaravelPasskeys\Models\Concerns;

interface HasPasskeys
{
    public function getPassKeyName(): string;

    public function getPassKeyId(): string;

    public function getPassKeyDisplayName(): string;
}

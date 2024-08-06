<?php

namespace Spatie\LaravelPasskeys\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\LaravelPasskeys\LaravelPasskeys
 */
class LaravelPasskeys extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Spatie\LaravelPasskeys\LaravelPasskeys::class;
    }
}

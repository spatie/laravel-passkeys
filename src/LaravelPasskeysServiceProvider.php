<?php

namespace Spatie\LaravelPasskeys;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPasskeys\Commands\LaravelPasskeysCommand;

class LaravelPasskeysServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-passkeys')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_passkeys_table');
    }
}

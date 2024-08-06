<?php

namespace Spatie\LaravelPasskeys;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPasskeys\Commands\LaravelPasskeysCommand;

class LaravelPasskeysServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-passkeys')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_passkeys_table')
            ->hasCommand(LaravelPasskeysCommand::class);
    }
}

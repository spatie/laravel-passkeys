<?php

namespace Spatie\LaravelPasskeys;

use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPasskeys\Http\Controllers\GeneratePasskeyRegisterOptionsController;

class LaravelPasskeysServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-passkeys')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_passkeys_table');

        $this->registerPasskeyRoutes();
    }

    protected function registerPasskeyRoutes(): self
    {
        Route::macro('passkeys', function (string $prefix = 'passkeys') {
            Route::prefix($prefix)->group(function () {
                Route::get('register', GeneratePasskeyRegisterOptionsController::class)->name('passkeys.register');
            });
        });

        return $this;
    }
}

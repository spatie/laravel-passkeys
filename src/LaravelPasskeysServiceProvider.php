<?php

namespace Spatie\LaravelPasskeys;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPasskeys\Http\Components\AuthenticatePasskeyComponent;
use Spatie\LaravelPasskeys\Http\Controllers\AuthenticateUsingPasskeyController;
use Spatie\LaravelPasskeys\Http\Controllers\GeneratePasskeyAuthenticationOptionsController;
use Spatie\LaravelPasskeys\Livewire\PasskeysComponent;

class LaravelPasskeysServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-passkeys')
            ->hasConfigFile()
            ->hasMigration('create_passkeys_table')
            ->hasViews()
            ->hasTranslations();

        $this
            ->registerPasskeyRouteMacro()
            ->registerComponents();
    }

    protected function registerPasskeyRouteMacro(): self
    {
        Route::macro('passkeys', function (string $prefix = 'passkeys') {
            Route::prefix($prefix)->group(function () {
                Route::get(
                    'authentication-options',
                    GeneratePasskeyAuthenticationOptionsController::class
                )->name('passkeys.authentication_options');

                Route::post(
                    'authenticate',
                    AuthenticateUsingPasskeyController::class
                )->name('passkeys.login');
            });
        });

        return $this;
    }

    public function registerComponents(): self
    {
        Blade::component('authenticate-passkey', AuthenticatePasskeyComponent::class);

        Livewire::component('passkeys', PasskeysComponent::class);

        return $this;
    }
}

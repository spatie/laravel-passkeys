<?php

use Livewire\Livewire;
use Spatie\LaravelPasskeys\Http\Controllers\AuthenticateUsingPasskeyController;
use Spatie\LaravelPasskeys\Livewire\PasskeysComponent;
use Spatie\LaravelPasskeys\Models\Passkey;
use Spatie\LaravelPasskeys\Tests\TestSupport\Models\Admin;
use Spatie\LaravelPasskeys\Tests\TestSupport\Models\User;

// We set up the multi-auth configuration before each test in this file.
beforeEach(function () {
    $this->loadMigrationsFrom(__DIR__ . '/../TestSupport/database/migrations');

    // Set up the multi-auth configuration for the tests
    config(['auth.providers.admins' => [
        'driver' => 'eloquent',
        'model' => Admin::class,
    ]]);
    config(['auth.guards.admin' => [
        'driver' => 'session',
        'provider' => 'admins',
    ]]);
    config(['passkeys.guards' => [
        'web' => [
            'authenticatable' => User::class,
            'redirect_to_after_login' => '/dashboard',
        ],
        'admin' => [
            'authenticatable' => Admin::class,
            'redirect_to_after_login' => '/admin/dashboard',
        ],
    ]]);
});


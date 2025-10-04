<?php

use Livewire\Livewire;
use Spatie\LaravelPasskeys\Http\Controllers\AuthenticateUsingPasskeyController;
use Spatie\LaravelPasskeys\Livewire\PasskeysComponent;
use Spatie\LaravelPasskeys\Models\Passkey;
use Spatie\LaravelPasskeys\Tests\TestSupport\Models\Admin;
use Spatie\LaravelPasskeys\Tests\TestSupport\Models\User;

it('can mount the PasskeysComponent', function () {
    $user = User::factory()->create();

    auth()->login($user);

    Livewire::test(PasskeysComponent::class)
        ->assertStatus(200);
});

it ('can mount the PasskeyComponent with not default guard', function () {
     $user = Admin::factory()->create();

     auth('admin')->login($user);

     Livewire::test(PasskeysComponent::class, ['guard' => 'admin'])
         ->assertStatus(200);
});

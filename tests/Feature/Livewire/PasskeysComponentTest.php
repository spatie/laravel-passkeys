<?php

use Livewire\Livewire;
use Spatie\LaravelPasskeys\Livewire\PasskeysComponent;
use Spatie\LaravelPasskeys\Tests\TestSupport\Models\User;

beforeEach(function () {
    $user = User::factory()->create();

    auth()->login($user);
});

it('can mount the PasskeysComponent', function () {
    Livewire::test(PasskeysComponent::class)
        ->assertStatus(200);
});

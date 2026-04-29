<?php

use Livewire\Livewire;
use Spatie\LaravelPasskeys\Livewire\PasskeysComponent;
use Spatie\LaravelPasskeys\Models\Passkey;
use Spatie\LaravelPasskeys\Tests\TestSupport\Models\User;

beforeEach(function () {
    $user = User::factory()->create();

    auth()->login($user);
});

it('can mount the PasskeysComponent', function () {
    Livewire::test(PasskeysComponent::class)
        ->assertStatus(200);
});

it('accepts a string identifier when deleting a passkey', function () {
    $passkey = Passkey::factory()->create([
        'authenticatable_id' => auth()->id(),
    ]);

    Livewire::test(PasskeysComponent::class)
        ->call('deletePasskey', '550e8400-e29b-41d4-a716-446655440000')
        ->assertStatus(200);

    expect(Passkey::find($passkey->id))->not->toBeNull();
});

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Spatie\LaravelPasskeys\Support\Config;
use Spatie\LaravelPasskeys\Tests\TestSupport\Models\User;

beforeEach(function () {
    Route::passkeys();

    $this->user = User::factory()->create();

    $this->actingAs($this->user);
});

it('can generate passkey register options', function () {
    $this->getJson(route('passkeys.register'))
        ->assertOk()
        ->assertJsonStructure(['challenge'])
        ->assertJsonFragment([
            'rp' => [
                'name' => Config::getRelyingPartyName(),
                'id' => Config::getRelyingPartyId(),
                'icon' => Config::getRelyingPartyIcon(),
            ],
            'user' => [
                'displayName' => $this->user->name,
                'id' => Str::before(base64_encode($this->user->id), '='),
                'name' => $this->user->email,
            ],
        ]);
});

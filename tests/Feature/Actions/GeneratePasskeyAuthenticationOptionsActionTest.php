<?php

use Illuminate\Support\Facades\Session;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyAuthenticationOptionsAction;
use Spatie\LaravelPasskeys\Support\Config;

beforeEach(function () {
    $this->action = Config::getAction(
        'generate_passkey_authentication_options',
        GeneratePasskeyAuthenticationOptionsAction::class,
    );
});

it('persists options across the flash lifecycle so intermediate requests do not consume them', function () {
    Session::start();

    $options = $this->action->execute();

    // Simulate the session middleware running for two intermediate
    // requests between the generate-options call and the authenticate POST
    // (e.g. a Livewire poll and an asset request). Flash data is gone after
    // a single intermediate request, so put-based storage is required.
    Session::ageFlashData();
    Session::ageFlashData();

    expect(Session::get('passkey-authentication-options'))->toBe($options);
});

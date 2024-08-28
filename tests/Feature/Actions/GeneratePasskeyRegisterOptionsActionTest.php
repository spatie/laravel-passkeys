<?php

use Spatie\LaravelPasskeys\Actions\GeneratePasskeyRegisterOptionsAction;
use Spatie\LaravelPasskeys\Support\Config;
use Spatie\LaravelPasskeys\Tests\TestSupport\Models\User;
use Webauthn\PublicKeyCredentialCreationOptions;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'user@example.com',
        'name' => 'John Doe',
    ]);

    $this->action = Config::getAction('generate_passkey_register_options', GeneratePasskeyRegisterOptionsAction::class);
});

it('can generate options to register a passkey as json', function () {
    $output = $this->action->execute($this->user);

    expect($output)
        ->toBeJson()
        ->toMatchSnapshot();
});

it('can generate options to register a passkey as an object', function () {
    $output = $this->action->execute($this->user, asJson: false);

    expect($output)->toBeInstanceOf(PublicKeyCredentialCreationOptions::class);
});

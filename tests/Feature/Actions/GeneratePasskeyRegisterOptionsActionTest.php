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

it('does not trigger deprecation warnings when generating register options', function () {
    $deprecations = [];

    set_error_handler(function (int $errno, string $errstr) use (&$deprecations): bool {
        $deprecations[] = $errstr;

        return true;
    }, E_USER_DEPRECATED);

    try {
        $this->action->execute($this->user);
    } finally {
        restore_error_handler();
    }

    expect($deprecations)->toBe([]);
});

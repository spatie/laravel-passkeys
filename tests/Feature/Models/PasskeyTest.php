<?php

use Spatie\LaravelPasskeys\Models\Passkey;
use Webauthn\PublicKeyCredentialSource;

it('can access the data attribute as a PublicKeyCredentialSource', function () {
    $passkey = Passkey::factory()->create();

    $passkey = $passkey->fresh();

    expect($passkey->data)->toBeInstanceOf(PublicKeyCredentialSource::class);
});

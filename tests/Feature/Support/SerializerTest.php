<?php

use Spatie\LaravelPasskeys\Support\Serializer;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialUserEntity;

it('does not trigger a deprecation when deserializing creation options with a relying party name', function () {
    $relyingPartyEntity = new PublicKeyCredentialRpEntity('', 'localhost');
    $relyingPartyEntity->name = 'My application';

    $options = new PublicKeyCredentialCreationOptions(
        rp: $relyingPartyEntity,
        user: new PublicKeyCredentialUserEntity('john', 'user-id', 'John Doe'),
        challenge: random_bytes(16),
        pubKeyCredParams: [],
    );

    $json = Serializer::make()->toJson($options);

    $deprecations = [];
    set_error_handler(function (int $level, string $message) use (&$deprecations): bool {
        $deprecations[] = $message;

        return true;
    }, E_USER_DEPRECATED);

    try {
        $deserialized = Serializer::make()->fromJson($json, PublicKeyCredentialCreationOptions::class);
    } finally {
        restore_error_handler();
    }

    expect($deserialized->rp->name)->toBe('My application');
    expect($deprecations)->toBe([]);
});

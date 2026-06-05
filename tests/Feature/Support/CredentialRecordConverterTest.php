<?php

use Spatie\LaravelPasskeys\Support\CredentialRecordConverter;
use Symfony\Component\Uid\Uuid;
use Webauthn\CredentialRecord;
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\TrustPath\EmptyTrustPath;

function aPublicKeyCredentialSource(): PublicKeyCredentialSource
{
    return new PublicKeyCredentialSource(
        'credential-id',
        PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY,
        ['internal'],
        'none',
        EmptyTrustPath::create(),
        Uuid::fromString('00000000-0000-0000-0000-000000000000'),
        'public-key',
        'user-handle',
        100,
    );
}

it('converts a PublicKeyCredentialSource to a plain CredentialRecord', function () {
    $credentialRecord = CredentialRecordConverter::toCredentialRecord(aPublicKeyCredentialSource());

    expect($credentialRecord)->toBeInstanceOf(CredentialRecord::class);
    expect($credentialRecord)->not->toBeInstanceOf(PublicKeyCredentialSource::class);
});

it('preserves all properties when converting to a CredentialRecord', function () {
    $source = aPublicKeyCredentialSource();

    $credentialRecord = CredentialRecordConverter::toCredentialRecord($source);

    expect($credentialRecord->publicKeyCredentialId)->toBe($source->publicKeyCredentialId);
    expect($credentialRecord->type)->toBe($source->type);
    expect($credentialRecord->transports)->toBe($source->transports);
    expect($credentialRecord->attestationType)->toBe($source->attestationType);
    expect($credentialRecord->trustPath)->toBe($source->trustPath);
    expect($credentialRecord->aaguid)->toBe($source->aaguid);
    expect($credentialRecord->credentialPublicKey)->toBe($source->credentialPublicKey);
    expect($credentialRecord->userHandle)->toBe($source->userHandle);
    expect($credentialRecord->counter)->toBe($source->counter);
});

<?php

namespace Spatie\LaravelPasskeys\Support;

use Webauthn\PublicKeyCredentialSource;

class CredentialRecordConverter
{
    /**
     * Ensure the given credential is a PublicKeyCredentialSource instance.
     *
     * In webauthn-lib 5.3+, validators return CredentialRecord instead of
     * PublicKeyCredentialSource. This method converts it back for backward
     * compatibility.
     */
    public static function toPublicKeyCredentialSource(mixed $credential): PublicKeyCredentialSource
    {
        if ($credential instanceof PublicKeyCredentialSource) {
            return $credential;
        }

        return new PublicKeyCredentialSource(
            publicKeyCredentialId: $credential->publicKeyCredentialId,
            type: $credential->type,
            transports: $credential->transports,
            attestationType: $credential->attestationType,
            trustPath: $credential->trustPath,
            aaguid: $credential->aaguid,
            credentialPublicKey: $credential->credentialPublicKey,
            userHandle: $credential->userHandle,
            counter: $credential->counter,
            otherUI: $credential->otherUI ?? null,
            backupEligible: $credential->backupEligible ?? null,
            backupStatus: $credential->backupStatus ?? null,
            uvInitialized: $credential->uvInitialized ?? null,
        );
    }
}

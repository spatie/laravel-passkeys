<?php

namespace Spatie\LaravelPasskeys\Support;

use Webauthn\CredentialRecord;
use Webauthn\PublicKeyCredentialSource;

class CredentialRecordConverter
{
    /**
     * Convert the given credential to a plain CredentialRecord instance.
     *
     * In webauthn-lib 5.3+, passing a PublicKeyCredentialSource to the
     * validators is deprecated. This method downcasts a stored
     * PublicKeyCredentialSource to a CredentialRecord so it can be passed to
     * the validators without triggering deprecation warnings.
     */
    public static function toCredentialRecord(CredentialRecord $credential): CredentialRecord
    {
        if (! $credential instanceof PublicKeyCredentialSource) {
            return $credential;
        }

        return new CredentialRecord(
            publicKeyCredentialId: $credential->publicKeyCredentialId,
            type: $credential->type,
            transports: $credential->transports,
            attestationType: $credential->attestationType,
            trustPath: $credential->trustPath,
            aaguid: $credential->aaguid,
            credentialPublicKey: $credential->credentialPublicKey,
            userHandle: $credential->userHandle,
            counter: $credential->counter,
            otherUI: $credential->otherUI,
            backupEligible: $credential->backupEligible,
            backupStatus: $credential->backupStatus,
            uvInitialized: $credential->uvInitialized,
        );
    }

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

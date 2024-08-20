<?php

namespace Spatie\LaravelPasskeys\Actions;

use Spatie\LaravelPasskeys\Exceptions\InvalidPasskey;
use Spatie\LaravelPasskeys\Exceptions\InvalidPasskeyOptions;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Passkey;
use Spatie\LaravelPasskeys\Support\Serializer;
use Throwable;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\CeremonyStep\CeremonyStepManagerFactory;
use Webauthn\PublicKeyCredential;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialSource;

class StorePasskeyAction
{
    public function execute(
        HasPasskeys $authenticatable,
        string $passkeyJson,
        string $passkeyOptionsJson,
        string $hostName,
        array $additionalProperties = [],
    ): Passkey {
        $publicKeyCredentialSource = $this->determinePublicKeyCredentialSource(
            $passkeyJson,
            $passkeyOptionsJson,
            $hostName
        );

        return $authenticatable->passkeys()->create([
            ...$additionalProperties,
            'data' => $publicKeyCredentialSource,
        ]);
    }

    protected function determinePublicKeyCredentialSource(
        string $passkeyJson,
        string $passkeyOptionsJson,
        string $hostName,
    ): PublicKeyCredentialSource {
        $passkeyOptions = $this->getPasskeyOptions($passkeyOptionsJson);

        $publicKeyCredential = $this->getPasskey($passkeyJson);

        if (! $publicKeyCredential->response instanceof AuthenticatorAttestationResponse) {
            throw InvalidPasskey::invalidPublicKeyCredential();
        }

        $csmFactory = new CeremonyStepManagerFactory;
        $creationCsm = $csmFactory->creationCeremony();

        try {
            $publicKeyCredentialSource = AuthenticatorAttestationResponseValidator::create($creationCsm)->check(
                authenticatorAttestationResponse: $publicKeyCredential->response,
                publicKeyCredentialCreationOptions: $passkeyOptions,
                host: $hostName,
            );
        } catch (Throwable $exception) {
            throw InvalidPasskey::invalidAuthenticatorAttestationResponse($exception);
        }

        return $publicKeyCredentialSource;
    }

    protected function getPasskeyOptions(string $passkeyOptionsJson): PublicKeyCredentialCreationOptions
    {
        if (! json_validate($passkeyOptionsJson)) {
            throw InvalidPasskeyOptions::invalidJson();
        }

        /** @var PublicKeyCredentialCreationOptions $passkeyOptions */
        $passkeyOptions = Serializer::make()->fromJson(
            $passkeyOptionsJson,
            PublicKeyCredentialCreationOptions::class
        );

        return $passkeyOptions;
    }

    protected function getPasskey(string $passkeyJson): PublicKeyCredential
    {
        if (! json_validate($passkeyJson)) {
            throw InvalidPasskey::invalidJson();
        }

        /** @var PublicKeyCredential $publicKeyCredential */
        $publicKeyCredential = Serializer::make()->fromJson(
            $passkeyJson,
            PublicKeyCredential::class
        );

        return $publicKeyCredential;
    }
}

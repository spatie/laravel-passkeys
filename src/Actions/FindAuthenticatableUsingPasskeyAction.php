<?php

namespace Spatie\LaravelPasskeys\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\LaravelPasskeys\Models\Passkey;
use Spatie\LaravelPasskeys\Support\Config;
use Spatie\LaravelPasskeys\Support\Serializer;
use Throwable;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\CeremonyStep\CeremonyStepManagerFactory;
use Webauthn\PublicKeyCredential;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\PublicKeyCredentialSource;

class FindAuthenticatableUsingPasskeyAction
{
    public function execute(
        string $publicKeyCredentialJson,
        string $passkeyOptionsJson,
    ): ?Authenticatable {
        $publicKeyCredential = $this->determinePublicKeyCredential($publicKeyCredentialJson);

        if (! $publicKeyCredential) {
            return null;
        }

        $passkey = $this->findPasskey($publicKeyCredential);

        if (! $passkey) {
            return null;
        }

        /** @var PublicKeyCredentialRequestOptions $passkeyOptions */
        $passkeyOptions = Serializer::make()->fromJson(
            $passkeyOptionsJson,
            PublicKeyCredentialRequestOptions::class,
        );

        $publicKeyCredentialSource = $this->determinePublicKeyCredentialSource(
            $publicKeyCredential,
            $passkeyOptions,
            $passkey,
        );

        if (! $publicKeyCredentialSource) {
            return null;
        }

        $passkey->update(['data' => $publicKeyCredentialSource]);

        return $passkey->authenticatable;
    }

    public function determinePublicKeyCredential(
        string $publicKeyCredentialJson,
    ): ?PublicKeyCredential {
        $publicKeyCredential = Serializer::make()->fromJson(
            $publicKeyCredentialJson,
            PublicKeyCredential::class,
        );

        if (! $publicKeyCredential->response instanceof AuthenticatorAssertionResponse) {
            return null;
        }

        return $publicKeyCredential;
    }

    protected function findPasskey(PublicKeyCredential $publicKeyCredential): ?Passkey
    {
        $passkeyModel = Config::getPassKeyModel();

        return $passkeyModel::firstWhere('credential_id', $publicKeyCredential->rawId);
    }

    protected function determinePublicKeyCredentialSource(
        PublicKeyCredential $publicKeyCredential,
        PublicKeyCredentialRequestOptions $passkeyOptions,
        Passkey $passkey,
    ): ?PublicKeyCredentialSource {
        $csmFactory = new CeremonyStepManagerFactory;
        $requestCsm = $csmFactory->requestCeremony();

        try {
            $validator = AuthenticatorAssertionResponseValidator::create($requestCsm);

            $publicKeyCredentialSource = $validator->check(
                publicKeyCredentialSource: $passkey->data,
                authenticatorAssertionResponse: $publicKeyCredential->response,
                publicKeyCredentialRequestOptions: $passkeyOptions,
                host: parse_url(config('app.url'), PHP_URL_HOST),
                userHandle: null,
            );
        } catch (Throwable) {
            return null;
        }

        return $publicKeyCredentialSource;
    }
}

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

class FindPasskeyToAuthenticateAction
{
    public function execute(
        string $publicKeyCredentialJson,
        string $passkeyOptionsJson,
    ): ?Passkey {
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

        $this->updatePasskey($passkey, $publicKeyCredentialSource);

        if (Config::isMultiAuthEnabled()) {
            $guard = $this->resolveGuard($passkey->authenticatable);

            if (is_null($guard)) {
                return null;
            }

            // We'll set the guard on the passkey model so we can use it later
            // in the pipeline, without having to resolve it again.
            $passkey->setAttribute('guard', $guard);
        }

        return $passkey;
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
        $passkeyModel = Config::getPasskeyModel();

        return $passkeyModel::firstWhere('credential_id', mb_convert_encoding($publicKeyCredential->rawId, 'UTF-8'));
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

    protected function updatePasskey(
        Passkey $passkey,
        PublicKeyCredentialSource $publicKeyCredentialSource
    ): self {
        $passkey->update([
            'data' => $publicKeyCredentialSource,
            'last_used_at' => now(),
        ]);

        return $this;
    }

    protected function resolveGuard(Authenticatable $authenticatable): ?string
    {
        $authenticatableClass = $authenticatable::class;

        foreach (config('auth.guards') as $guard => $config) {
            $provider = config("auth.providers.{$config['provider']}");

            if (($provider['model'] ?? null) === $authenticatableClass) {
                return $guard;
            }
        }

        return null;
    }
}

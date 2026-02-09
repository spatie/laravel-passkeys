<?php

namespace Spatie\LaravelPasskeys\Actions;

use Spatie\LaravelPasskeys\Models\Passkey;
use Spatie\LaravelPasskeys\Support\Config;
use Spatie\LaravelPasskeys\Support\CredentialRecordConverter;
use Spatie\LaravelPasskeys\Support\Serializer;
use Throwable;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;
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
        $passkeyModel = Config::getPassKeyModel();

        return $passkeyModel::firstWhere('credential_id', $passkeyModel::encodeCredentialId($publicKeyCredential->rawId));
    }

    protected function determinePublicKeyCredentialSource(
        PublicKeyCredential $publicKeyCredential,
        PublicKeyCredentialRequestOptions $passkeyOptions,
        Passkey $passkey,
    ): ?PublicKeyCredentialSource {
        $configureCeremonyStepManagerFactoryAction = Config::getAction(
            'configure_ceremony_step_manager_factory',
            ConfigureCeremonyStepManagerFactoryAction::class
        );
        $csmFactory = $configureCeremonyStepManagerFactoryAction->execute();
        $requestCsm = $csmFactory->requestCeremony();

        try {
            $validator = AuthenticatorAssertionResponseValidator::create($requestCsm);

            $publicKeyCredentialSource = $validator->check(
                $passkey->data,
                $publicKeyCredential->response,
                $passkeyOptions,
                parse_url(config('app.url'), PHP_URL_HOST),
                null,
            );
        } catch (Throwable) {
            return null;
        }

        return CredentialRecordConverter::toPublicKeyCredentialSource($publicKeyCredentialSource);
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
}

<?php

namespace Spatie\LaravelPasskeys\Actions;

use Illuminate\Support\Str;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Support\Config;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\AttestationStatement\NoneAttestationStatementSupport;
use Webauthn\Denormalizer\WebauthnSerializerFactory;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialUserEntity;

class GeneratePasskeyOptionsAction
{
    public function execute(HasPasskeys $authenticatable, bool $asJson = false): string|PublicKeyCredentialCreationOptions
    {
        $options = new PublicKeyCredentialCreationOptions(
            rp: $this->relatedPartyEntity(),
            user: $this->generateUserEntity($authenticatable),
            challenge: $this->challenge(),
            // TODO: consider adding selection
            // authenticatorSelection:
        );

        if ($asJson) {
            $options = $this->convertToJson($options);
        }

        return $options;
    }

    protected function relatedPartyEntity(): PublicKeyCredentialRpEntity
    {
        return new PublicKeyCredentialRpEntity(
            name: Config::getRelyingPartyName(),
            id: Config::getRelyingPartyId(),
            icon: Config::getRelyingPartyIcon(),
        );
    }

    public function generateUserEntity(HasPasskeys $authenticatable): PublicKeyCredentialUserEntity
    {
        return new PublicKeyCredentialUserEntity(
            name: $authenticatable->getPassKeyName(),
            id: $authenticatable->getPassKeyId(),
            displayName: $authenticatable->getPassKeyDisplayName(),
        );
    }

    protected function challenge(): string
    {
        return Str::random();
    }

    protected function convertToJson(PublicKeyCredentialCreationOptions $options): string
    {
        $attestationStatementSupportManager = AttestationStatementSupportManager::create();
        $attestationStatementSupportManager->add(NoneAttestationStatementSupport::create());

        $factory = new WebauthnSerializerFactory($attestationStatementSupportManager);

        $serializer = $factory->create();

        return $serializer->serialize($options, 'json');
    }
}

<?php

namespace Spatie\LaravelPasskeys\Actions;

use Illuminate\Support\Str;
use ReflectionProperty;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Support\Config;
use Spatie\LaravelPasskeys\Support\Serializer;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialEntity;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialUserEntity;

class GeneratePasskeyRegisterOptionsAction
{
    public function execute(
        HasPasskeys $authenticatable,
        bool $asJson = true,
    ): string|PublicKeyCredentialCreationOptions {
        $options = new PublicKeyCredentialCreationOptions(
            rp: $this->relatedPartyEntity(),
            user: $this->generateUserEntity($authenticatable),
            challenge: $this->challenge(),
            authenticatorSelection: $this->authenticatorSelection(),
            attestation: PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_NONE,
        );

        if ($asJson) {
            $options = Serializer::make()->toJson($options);
        }

        return $options;
    }

    protected function relatedPartyEntity(): PublicKeyCredentialRpEntity
    {
        $name = Config::getRelyingPartyName();
        $id = Config::getRelyingPartyId();
        $icon = Config::getRelyingPartyIcon();

        // In web-auth/webauthn-lib 5.3+, passing a name to the constructor is
        // deprecated in favour of assigning the public `name` property. In
        // older versions that property is readonly, so the name still has to
        // be passed through the constructor.
        if ($this->entityNameIsWritable()) {
            $entity = new PublicKeyCredentialRpEntity(name: '', id: $id, icon: $icon);
            $entity->name = $name;

            return $entity;
        }

        return new PublicKeyCredentialRpEntity(name: $name, id: $id, icon: $icon);
    }

    protected function entityNameIsWritable(): bool
    {
        return ! (new ReflectionProperty(PublicKeyCredentialEntity::class, 'name'))->isReadOnly();
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

    public function authenticatorSelection(): AuthenticatorSelectionCriteria
    {
        return new AuthenticatorSelectionCriteria(
            null,
            AuthenticatorSelectionCriteria::USER_VERIFICATION_REQUIREMENT_PREFERRED,
            AuthenticatorSelectionCriteria::RESIDENT_KEY_REQUIREMENT_REQUIRED,
        );
    }
}

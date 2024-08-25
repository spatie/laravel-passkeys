<?php

namespace Spatie\LaravelPasskeys\Actions;

use Illuminate\Support\Str;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Support\Config;
use Spatie\LaravelPasskeys\Support\Serializer;
use Webauthn\PublicKeyCredentialCreationOptions;
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
            // TODO: consider adding selection
            // authenticatorSelection:
        );

        if ($asJson) {
            $options = Serializer::make()->toJson($options);
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
}

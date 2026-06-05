<?php

namespace Spatie\LaravelPasskeys\Support;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webauthn\PublicKeyCredentialRpEntity;

/**
 * web-auth/webauthn-lib only ships a normalizer for PublicKeyCredentialRpEntity,
 * so denormalization falls back to Symfony's ObjectNormalizer. That passes the
 * relying party name to the constructor, which is deprecated since webauthn-lib
 * 5.3. We denormalize the entity ourselves and assign the name through the
 * property to avoid the deprecation warning on every passkey registration.
 */
class PublicKeyCredentialRpEntityDenormalizer implements DenormalizerInterface
{
    /**
     * @param  array{id?: ?string, name?: ?string}  $data
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): PublicKeyCredentialRpEntity
    {
        $entity = new PublicKeyCredentialRpEntity('', $data['id'] ?? null);

        if (! empty($data['name'])) {
            $entity->name = $data['name'];
        }

        return $entity;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === PublicKeyCredentialRpEntity::class;
    }

    /**
     * @return array<class-string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            PublicKeyCredentialRpEntity::class => true,
        ];
    }
}

<?php

namespace Spatie\LaravelPasskeys\Support;

use ReflectionProperty;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\Denormalizer\WebauthnSerializerFactory;

class Serializer
{
    public static function make(): self
    {
        $attestationStatementSupportManager = AttestationStatementSupportManager::create();

        /** @var SymfonySerializer $webauthnSerializer */
        $webauthnSerializer = (new WebauthnSerializerFactory($attestationStatementSupportManager))->create();

        return new self(static::registerRpEntityDenormalizer($webauthnSerializer));
    }

    protected static function registerRpEntityDenormalizer(SymfonySerializer $webauthnSerializer): SymfonySerializer
    {
        // The factory does not expose its normalizers, so we read them to rebuild
        // the serializer with our relying party denormalizer taking precedence.
        $normalizers = (new ReflectionProperty(SymfonySerializer::class, 'normalizers'))->getValue($webauthnSerializer);

        return new SymfonySerializer(
            [new PublicKeyCredentialRpEntityDenormalizer, ...$normalizers],
            [new JsonEncoder],
        );
    }

    public function __construct(
        protected SymfonySerializer $serializer,
    ) {}

    public function toJson(mixed $value): string
    {
        return $this->serializer->serialize(
            $value,
            'json',
            [
                AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
                JsonEncode::OPTIONS => JSON_THROW_ON_ERROR,
            ]
        );
    }

    /**
     * @param  class-string  $desiredClass
     */
    public function fromJson(string $value, string $desiredClass): mixed
    {
        return $this
            ->serializer
            ->deserialize($value, $desiredClass, 'json');
    }
}

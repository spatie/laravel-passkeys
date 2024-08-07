<?php

namespace Spatie\LaravelPasskeys\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\LaravelPasskeys\Models\Passkey;
use Spatie\LaravelPasskeys\Support\Config;
use Symfony\Component\Uid\Uuid;
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\TrustPath\EmptyTrustPath;

class PasskeyFactory extends Factory
{
    protected $model = Passkey::class;

    public function definition()
    {
        $authModel = Config::getAuthenticatableModel();

        return [
            'name' => $this->faker->word,
            'authenticatable_id' => $authModel::factory(),
            'credential_id' => $this->faker->sentence,
            'data' => $this->dummyPublicKeyCredentialSource(),
        ];
    }

    protected function dummyPublicKeyCredentialSource(): PublicKeyCredentialSource
    {
        return PublicKeyCredentialSource::create(
            base64_decode(
                'eHouz/Zi7+BmByHjJ/tx9h4a1WZsK4IzUmgGjkhyOodPGAyUqUp/B9yUkflXY3yHWsNtsrgCXQ3HjAIFUeZB+w==',
                true
            ),
            PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY,
            [],
            'none',
            $trustPath ?? EmptyTrustPath::create(),
            Uuid::fromString('00000000-0000-0000-0000-000000000000'),
            base64_decode(
                'pQECAyYgASFYIJV56vRrFusoDf9hm3iDmllcxxXzzKyO9WruKw4kWx7zIlgg/nq63l8IMJcIdKDJcXRh9hoz0L+nVwP1Oxil3/oNQYs=',
                true
            ),
            'foo',
            100,
        );
    }
}

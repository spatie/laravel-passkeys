<?php

use Illuminate\Support\Facades\Event;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;
use Spatie\LaravelPasskeys\Events\PasskeyRegisteredEvent;
use Spatie\LaravelPasskeys\Support\CredentialRecordConverter;
use Spatie\LaravelPasskeys\Tests\TestSupport\Models\User;
use Symfony\Component\Uid\Uuid;
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\TrustPath\EmptyTrustPath;

it('fires a PasskeyRegisteredEvent when a passkey is stored', function () {
    Event::fake();

    $user = User::factory()->create();

    $action = new class extends StorePasskeyAction
    {
        protected function determinePublicKeyCredentialSource(
            string $passkeyJson,
            string $passkeyOptionsJson,
            string $hostName,
        ): PublicKeyCredentialSource {
            return CredentialRecordConverter::toPublicKeyCredentialSource(PublicKeyCredentialSource::create(
                base64_decode('eHouz/Zi7+BmByHjJ/tx9h4a1WZsK4IzUmgGjkhyOodPGAyUqUp/B9yUkflXY3yHWsNtsrgCXQ3HjAIFUeZB+w==', true),
                PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY,
                [],
                'none',
                EmptyTrustPath::create(),
                Uuid::fromString('00000000-0000-0000-0000-000000000000'),
                base64_decode('pQECAyYgASFYIJV56vRrFusoDf9hm3iDmllcxxXzzKyO9WruKw4kWx7zIlgg/nq63l8IMJcIdKDJcXRh9hoz0L+nVwP1Oxil3/oNQYs=', true),
                'foo',
                100,
            ));
        }
    };

    $action->execute($user, '{}', '{}', 'localhost', ['name' => 'My Passkey']);

    Event::assertDispatched(PasskeyRegisteredEvent::class, function (PasskeyRegisteredEvent $event) use ($user) {
        return $event->passkey->name === 'My Passkey'
            && $event->passkey->authenticatable_id === $user->getKey();
    });
});

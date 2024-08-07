<?php

namespace Spatie\LaravelPasskeys\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\CeremonyStep\CeremonyStepManagerFactory;
use Webauthn\Denormalizer\WebauthnSerializerFactory;
use Webauthn\PublicKeyCredential;

class StorePasskeyController
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'passkey' => ['required', 'json'],
        ]);

        try {
            /** @var PublicKeyCredential $publicKeyCredential */
            $publicKeyCredential = (new WebauthnSerializerFactory(AttestationStatementSupportManager::create()))
                ->create()
                ->deserialize($data['passkey'], PublicKeyCredential::class, 'json');
        } catch (Throwable $exception) {
            throw ValidationException::withMessages([
                // todo: make translatable
                'name' => 'The given passkey is invalid',
            ])->errorBag('createPasskey');
        }

        if (! $publicKeyCredential->response instanceof AuthenticatorAttestationResponse) {
            throw new Exception('Invalid passkey');
        }

        $csmFactory = new CeremonyStepManagerFactory;

        $creationCsm = $csmFactory->creationCeremony();

        $publicKeyCredentialSource = AuthenticatorAttestationResponseValidator::create($creationCsm)->check(
            authenticatorAttestationResponse: $publicKeyCredential->response,
            publicKeyCredentialCreationOptions: $request->session()->get('passkey-registration-options'),
            host: $request->getHost(),
        );

        $request->user()->passkeys()->create([
            'name' => $data['name'],
            'credential_id' => $publicKeyCredentialSource->publicKeyCredentialId,
            'data' => $publicKeyCredentialSource,
        ]);
    }
}

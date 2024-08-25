<?php

namespace Spatie\LaravelPasskeys\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelPasskeys\Models\Passkey;
use Spatie\LaravelPasskeys\Support\Serializer;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\CeremonyStep\CeremonyStepManagerFactory;
use Webauthn\Denormalizer\WebauthnSerializerFactory;
use Webauthn\PublicKeyCredential;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialRequestOptions;

class LoginWithPasskeyController
{
    public function __invoke(Request $request)
    {
        ray()->clearScreen();

        $data = $request->validate(['answer' => ['required', 'json']]);


        $publicKeyCredential = Serializer::make()->fromJson($data['answer'], PublicKeyCredential::class);

        /*
        if (! $publicKeyCredential->response instanceof AuthenticatorAssertionResponse) {
            return to_route('profile.edit')->withFragment('managePasskeys');
        }
        */

        $passkey = Passkey::firstWhere('credential_id', $publicKeyCredential->rawId);

        if (! $passkey) {
            throw new \Exception('This passkey is not valid');
        }


        $csmFactory = new CeremonyStepManagerFactory;
        $requestCsm = $csmFactory->requestCeremony();

        /** @var PublicKeyCredentialRequestOptions $passkeyOptions */
        $passkeyOptions = Serializer::make()->fromJson(
            Session::get('passkey-authentication-options'),
            PublicKeyCredentialRequestOptions::class
        );

        try {
            $validator = AuthenticatorAssertionResponseValidator::create($requestCsm);

            $publicKeyCredentialSource = $validator->check(
                publicKeyCredentialSource: $passkey->data,
                authenticatorAssertionResponse: $publicKeyCredential->response,
                publicKeyCredentialRequestOptions: $passkeyOptions,
                host: $request->getHost(),
                userHandle: null,
            );

            ray('public key credential source determined');
        } catch (\Throwable $e) {
            ray('nope', $e->getMessage())->red();
            throw ValidationException::withMessages([
                'answer' => 'This passkey is not valid.'
            ]);
        }

        ray('valid!!');

        $passkey->update(['data' => $publicKeyCredentialSource]);

        ray($passkey->authenticatable);
        auth()->login($passkey->authenticatable);

        $request->session()->regenerate();

        ray('logged IN')->green();
        return redirect()->route('dashboard');
    }
}

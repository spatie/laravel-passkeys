<?php

namespace Spatie\LaravelPasskeys\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelPasskeys\Http\Requests\AuthenticateUsingPasskeysRequest;
use Spatie\LaravelPasskeys\Models\Passkey;
use Spatie\LaravelPasskeys\Support\Serializer;
use Throwable;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\CeremonyStep\CeremonyStepManagerFactory;
use Webauthn\PublicKeyCredential;
use Webauthn\PublicKeyCredentialRequestOptions;

class AuthenticateUsingPasskeyController
{
    public function __invoke(AuthenticateUsingPasskeysRequest $request)
    {
        $publicKeyCredential = Serializer::make()->fromJson(
            $request->get('answer'),
            PublicKeyCredential::class
        );

        /*
        if (! $publicKeyCredential->response instanceof AuthenticatorAssertionResponse) {
            return to_route('profile.edit')->withFragment('managePasskeys');
        }
        */

        $passkey = Passkey::firstWhere('credential_id', $publicKeyCredential->rawId);

        if (! $passkey) {
            throw new Exception('This passkey is not valid');
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
        } catch (Throwable $e) {
            throw ValidationException::withMessages([
                'answer' => 'This passkey is not valid.'
            ]);
        }

        $passkey->update(['data' => $publicKeyCredentialSource]);

        auth()->login($passkey->authenticatable);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}

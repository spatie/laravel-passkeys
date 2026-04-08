<?php

namespace Spatie\LaravelPasskeys\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Spatie\LaravelPasskeys\Actions\FindPasskeyToAuthenticateAction;
use Spatie\LaravelPasskeys\Events\PasskeyUsedToAuthenticateEvent;
use Spatie\LaravelPasskeys\Http\Requests\AuthenticateUsingPasskeysRequest;
use Spatie\LaravelPasskeys\Models\Passkey;
use Spatie\LaravelPasskeys\Support\Config;

class AuthenticateUsingPasskeyController
{
    public function __invoke(AuthenticateUsingPasskeysRequest $request)
    {
        $findAuthenticatableUsingPasskey = Config::getAction(
            'find_passkey',
            FindPasskeyToAuthenticateAction::class
        );

        $passkey = $findAuthenticatableUsingPasskey->execute(
            $request->input('start_authentication_response'),
            Session::get('passkey-authentication-options'),
        );

        if (! $passkey) {
            return $this->invalidPasskeyResponse();
        }

        /** @var Authenticatable $authenticatable */
        $authenticatable = $passkey->authenticatable;

        if (! $authenticatable) {
            return $this->invalidPasskeyResponse();
        }

        $this->logInAuthenticatable($authenticatable, $request->boolean('remember'));

        $this->firePasskeyEvent($passkey, $request);

        return $this->validPasskeyResponse($request);
    }

    protected function logInAuthenticatable(Authenticatable $authenticatable, bool $remember = false): self
    {
        auth()->login($authenticatable, $remember);

        Session::regenerate();

        return $this;
    }

    protected function validPasskeyResponse(Request $request): RedirectResponse
    {
        $url = Session::has('passkeys.redirect')
            ? Session::pull('passkeys.redirect')
            : Config::getRedirectAfterLogin();

        return redirect($url);
    }

    protected function invalidPasskeyResponse(): RedirectResponse
    {
        session()->flash('authenticatePasskey::message', __('passkeys::passkeys.invalid'));

        return back();
    }

    protected function firePasskeyEvent(Passkey $passkey, AuthenticateUsingPasskeysRequest $request): self
    {
        event(new PasskeyUsedToAuthenticateEvent($passkey, $request));

        return $this;
    }
}

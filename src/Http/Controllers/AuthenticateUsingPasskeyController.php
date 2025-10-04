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
            $request->get('start_authentication_response'),
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

        $guard = $passkey->guard ?? null;

        $this->logInAuthenticatable($authenticatable, $guard);

        $this->firePasskeyEvent($passkey, $request);

        return $this->validPasskeyResponse($request, $guard);
    }

    protected function logInAuthenticatable(Authenticatable $authenticatable, ?string $guard = null): self
    {
        auth()->guard($guard)->login($authenticatable);

        Session::regenerate();

        return $this;
    }

    protected function validPasskeyResponse(Request $request, ?string $guard = null): RedirectResponse
    {
        $url = Session::has('passkeys.redirect')
            ? Session::pull('passkeys.redirect')
            : Config::getRedirectAfterLogin($guard);

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

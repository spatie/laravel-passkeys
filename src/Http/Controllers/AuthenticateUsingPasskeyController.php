<?php

namespace Spatie\LaravelPasskeys\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Spatie\LaravelPasskeys\Actions\FindAuthenticatableUsingPasskeyAction;
use Spatie\LaravelPasskeys\Http\Requests\AuthenticateUsingPasskeysRequest;
use Spatie\LaravelPasskeys\Support\Config;

class AuthenticateUsingPasskeyController
{
    public function __invoke(AuthenticateUsingPasskeysRequest $request)
    {
        /**
         * @var FindAuthenticatableUsingPasskeyAction $findAuthenticatableUsingPasskey
         */
        $findAuthenticatableUsingPasskey = Config::getAction(
            'find_authenticatable_using_passkey',
            FindAuthenticatableUsingPasskeyAction::class
        );

        $authenticatable = $findAuthenticatableUsingPasskey->execute(
            $request->get('answer'),
            Session::get('passkey-authentication-options'),
        );

        if (! $authenticatable) {
            return $this->invalidPasskeyResponse();
        }

        $this->logInAuthenticatable($authenticatable);

        return $this->validPasskeyResponse($request);
    }

    public function logInAuthenticatable(Authenticatable $authenticatable): self
    {
        auth()->login($authenticatable);

        Session::regenerate();

        return $this;
    }

    public function validPasskeyResponse(Request $request): RedirectResponse
    {
        $url = $request->has('redirect')
            ? $request->get('redirect')
            : config('passkeys.redirect_to_after_login');

        return redirect($url);
    }

    protected function invalidPasskeyResponse(): RedirectResponse
    {
        session()->flash('authenticatePasskey::message', __('passkeys::passkeys.invalid'));

        return back();
    }
}

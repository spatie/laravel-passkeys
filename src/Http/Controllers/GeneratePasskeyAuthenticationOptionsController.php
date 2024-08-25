<?php

namespace Spatie\LaravelPasskeys\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyAuthenticationOptionsAction;
use Spatie\LaravelPasskeys\Support\Config;

class GeneratePasskeyAuthenticationOptionsController
{
    public function __invoke()
    {
        /** @var GeneratePasskeyAuthenticationOptionsAction $action */
        $action = Config::getAction('generate_passkey_authentication_options', GeneratePasskeyAuthenticationOptionsAction::class);

        $options = $action->execute();

        Session::flash('passkey-registration-options', $options);

        return $options;
    }
}

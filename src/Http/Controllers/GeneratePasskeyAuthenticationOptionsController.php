<?php

namespace Spatie\LaravelPasskeys\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyOptionsAction;
use Spatie\LaravelPasskeys\Support\Config;

class GeneratePasskeyAuthenticationOptionsController
{
    public function __invoke()
    {
        /** @var GeneratePasskeyOptionsAction $action */
        $action = Config::getAction('generate_passkey_options', GeneratePasskeyOptionsAction::class);

        $options = $action->execute($this->getUser());

        Session::flash('passkey-registration-options', $options);

        return $options;
    }

    protected function getUser()
    {
        return auth()->user();
    }
}

<?php

namespace Spatie\LaravelPasskeys\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyOptionsAction;
use Spatie\LaravelPasskeys\Support\Config;

class GeneratePasskeyRegisterOptionsController
{
    public function __invoke()
    {
        $actionClass = Config::getActionClass('generate_passkey_options', GeneratePasskeyOptionsAction::class);

        /** @var GeneratePasskeyOptionsAction $action */
        $action = new $actionClass;

        $options = $action->execute($this->getUser());

        Session::flash('passkey-registration-options', $options);

        return $options;
    }

    protected function getUser()
    {
        return auth()->user();
    }
}

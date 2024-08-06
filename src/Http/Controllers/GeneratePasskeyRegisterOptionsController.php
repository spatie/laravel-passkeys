<?php

namespace Spatie\LaravelPasskeys\Http\Controllers;

use Spatie\LaravelPasskeys\Actions\GeneratePasskeyOptionsAction;
use Spatie\LaravelPasskeys\Support\Config;
use Webauthn\PublicKeyCredentialCreationOptions;

class GeneratePasskeyRegisterOptionsController
{
    public function __invoke(): PublicKeyCredentialCreationOptions
    {
        $actionClass = Config::getAction('generate_passkey_options', GeneratePasskeyOptionsAction::class);

        /** @var GeneratePasskeyOptionsAction $action */
        $action = new $actionClass;

        $options = $action->execute($this->getUser());

        return $options;
    }

    protected function getUser()
    {
        return auth()->user();
    }
}

<?php

namespace Spatie\LaravelPasskeys\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyOptionsAction;
use Spatie\LaravelPasskeys\Support\Config;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\AttestationStatement\NoneAttestationStatementSupport;
use Webauthn\Denormalizer\WebauthnSerializerFactory;

class GeneratePasskeyRegisterOptionsController
{
    public function __invoke()
    {
        $actionClass = Config::getAction('generate_passkey_options', GeneratePasskeyOptionsAction::class);

        /** @var GeneratePasskeyOptionsAction $action */
        $action = new $actionClass;

        $options = $action->execute($this->getUser());

        $attestationStatementSupportManager = AttestationStatementSupportManager::create();
        $attestationStatementSupportManager->add(NoneAttestationStatementSupport::create());

        $factory = new WebauthnSerializerFactory($attestationStatementSupportManager);

        $serializer = $factory->create();

        $optionsJson = $serializer->serialize($options, 'json');

        Session::flash('passkey-registration-options', $optionsJson);

        return $optionsJson;
    }

    protected function getUser()
    {
        return auth()->user();
    }
}

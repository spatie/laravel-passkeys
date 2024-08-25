<?php

namespace Spatie\LaravelPasskeys\Actions;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Spatie\LaravelPasskeys\Support\Serializer;
use Webauthn\PublicKeyCredentialRequestOptions;

class GeneratePasskeyAuthenticationOptionsAction
{
    public function execute(): string
    {
        $options = new PublicKeyCredentialRequestOptions(
            challenge: Str::random(),
            rpId: parse_url(config('app.url'), PHP_URL_HOST),
            allowCredentials: [],
        );

        $options = Serializer::make()->toJson($options);

        Session::flash('passkey-authentication-options', $options);

        return $options;
    }
}

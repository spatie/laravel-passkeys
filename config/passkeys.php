<?php

return [
    'relying_party' => [
        'name' => config('app.name'),
        'id' => parse_url(config('app.url'), PHP_URL_HOST),
        'icon' => null,
    ],

    'models' => [
        'passkey' => \Spatie\LaravelPasskeys\Models\Passkey::class,
        'authenticatable' => null,
    ],

    'actions' => [
        'generate_passkey_options' => \Spatie\LaravelPasskeys\Actions\GeneratePasskeyOptionsAction::class,
    ]
];

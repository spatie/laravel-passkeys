<?php

return [
    /*
     * After a successful authentication attempt using a passkey
     * we'll redirect to this URL.
     *
     * @deprecated Use the `guards` configuration instead.
     */
    'redirect_to_after_login' => '/dashboard',

    /*
     * These class are responsible for performing core tasks regarding passkeys.
     * You can customize them by creating a class that extends the default, and
     * by specifying your custom class name here.
     */
    'actions' => [
        'generate_passkey_register_options' => Spatie\LaravelPasskeys\Actions\GeneratePasskeyRegisterOptionsAction::class,
        'store_passkey' => Spatie\LaravelPasskeys\Actions\StorePasskeyAction::class,
        'generate_passkey_authentication_options' => \Spatie\LaravelPasskeys\Actions\GeneratePasskeyAuthenticationOptionsAction::class,
        'find_passkey' => Spatie\LaravelPasskeys\Actions\FindPasskeyToAuthenticateAction::class,
    ],

    /*
     * These properties will be used to generate the passkey.
     */
    'relying_party' => [
        'name' => config('app.name'),
        'id' => parse_url(config('app.url'), PHP_URL_HOST),
        'icon' => null,
    ],

    /*
     * The models used by the package.
     * We recommend using the `passkey_model` and `guards` keys instead.
     */
    'models' => [
        /*
         * @deprecated Use the `passkey_model` configuration instead.
         */
        'passkey' => Spatie\LaravelPasskeys\Models\Passkey::class,
        /*
         * @deprecated Use the `guards` configuration instead.
         */
        'authenticatable' => env('AUTH_MODEL', App\Models\User::class),
    ],

    /*
     * The model used for passkeys.
     * This is the recommended way to specify the passkey model.
     */
    'passkey_model' => Spatie\LaravelPasskeys\Models\Passkey::class,

    /*
     * Here you can define the configuration for each of your guards
     * to support multiple authenticatable models.
     */
    'guards' => [
        // 'web' => [
        //     'authenticatable' => App\Models\User::class,
        //     'redirect_to_after_login' => '/dashboard',
        // ],
    ],
];

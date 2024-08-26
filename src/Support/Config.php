<?php

namespace Spatie\LaravelPasskeys\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\LaravelPasskeys\Exceptions\InvalidActionClass;
use Spatie\LaravelPasskeys\Exceptions\InvalidAuthenticatableModel;
use Spatie\LaravelPasskeys\Exceptions\InvalidPasskeyModel;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Passkey;

class Config
{
    /**
     * @return class-string<\Spatie\LaravelPasskeys\Models\Passkey>
     */
    public static function getPassKeyModel(): string
    {
        $passkeyModel = config('passkeys.models.passkey');

        if (! is_a($passkeyModel, Passkey::class, true)) {
            throw InvalidPasskeyModel::make($passkeyModel);
        }

        return config('passkeys.models.passkey');
    }

    /**
     * @return class-string<\Illuminate\Auth\Authenticatable>
     */
    public static function getAuthenticatableModel(): string
    {
        $authenticatableModel = config('passkeys.models.authenticatable');

        foreach ([Authenticatable::class, HasPasskeys::class] as $interface) {
            if (! is_a($authenticatableModel, $interface, true)) {
                throw InvalidAuthenticatableModel::missingInterface($authenticatableModel, $interface);
            }
        }

        return $authenticatableModel;
    }

    public static function getRelyingPartyName(): string
    {
        return config('passkeys.relying_party.name');
    }

    public static function getRelyingPartyId(): string
    {
        return config('passkeys.relying_party.id');
    }

    public static function getRelyingPartyIcon(): ?string
    {
        return config('passkeys.relying_party.icon');
    }

    public static function getActionClass(string $actionName, string $actionBaseClass): string
    {
        $actionClass = config("passkeys.actions.{$actionName}");

        self::ensureValidActionClass($actionName, $actionBaseClass, $actionClass);

        return config("passkeys.actions.{$actionName}");
    }

    public static function getAction(string $actionName, string $actionBaseClass)
    {
        $actionClass = self::getActionClass($actionName, $actionBaseClass);

        return new $actionClass;
    }

    protected static function ensureValidActionClass(string $actionName, string $actionBaseClass, string $actionClass): void
    {
        if (! is_a($actionClass, $actionBaseClass, true)) {
            throw InvalidActionClass::make($actionName, $actionBaseClass, $actionClass);
        }
    }
}

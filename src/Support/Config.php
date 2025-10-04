<?php

namespace Spatie\LaravelPasskeys\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\LaravelPasskeys\Exceptions\GuardNotConfigured;
use Spatie\LaravelPasskeys\Exceptions\InvalidActionClass;
use Spatie\LaravelPasskeys\Exceptions\InvalidAuthenticatableModel;
use Spatie\LaravelPasskeys\Exceptions\InvalidPasskeyModel;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Passkey;

class Config
{
    /**
     * @return class-string<Passkey>
     */
    public static function getPasskeyModel(): string
    {
        $passkeyModel = config('passkeys.passkey_model') ?? config('passkeys.models.passkey');

        if (! is_a($passkeyModel, Passkey::class, true)) {
            throw InvalidPasskeyModel::make($passkeyModel);
        }

        return $passkeyModel;
    }

    /** @return class-string<Authenticatable> */
    public static function getAuthenticatableModel(?string $guard = null): string
    {
        if (! self::isMultiAuthEnabled()) {
            /** @var class-string<Authenticatable> $authenticatableModel */
            $authenticatableModel = config('passkeys.models.authenticatable');
        } else {
            $guard ??= self::getDefaultGuard();

            /** @var class-string<Authenticatable> $authenticatableModel */
            $authenticatableModel = self::getGuardConfig($guard, 'authenticatable');
        }

        foreach ([Authenticatable::class, HasPasskeys::class] as $interface) {
            if (! is_a($authenticatableModel, $interface, true)) {
                throw InvalidAuthenticatableModel::missingInterface($authenticatableModel, $interface);
            }
        }

        return $authenticatableModel;
    }

    public static function getRedirectAfterLogin(?string $guard = null): string
    {
        if (! self::isMultiAuthEnabled()) {
            return redirect()->intended(config('passkeys.redirect_to_after_login'))->getTargetUrl();
        }

        $guard ??= self::getDefaultGuard();

        $redirectTo = self::getGuardConfig($guard, 'redirect_to_after_login');

        return redirect()->intended($redirectTo)->getTargetUrl();
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

    /**
     * @template T
     *
     * @param  class-string<T>  $actionBaseClass
     * @return class-string<T>
     */
    public static function getActionClass(string $actionName, string $actionBaseClass): string
    {
        $actionClass = config("passkeys.actions.{$actionName}");

        self::ensureValidActionClass($actionName, $actionBaseClass, $actionClass);

        return $actionClass;
    }

    /**
     * @template T
     *
     * @param  class-string<T>  $actionBaseClass
     * @return T
     */
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

    public static function isMultiAuthEnabled(): bool
    {
        return ! empty(config('passkeys.guards'));
    }

    protected static function getDefaultGuard(): string
    {
        return config('auth.defaults.guard');
    }

    /**
     * @return mixed
     */
    protected static function getGuardConfig(string $guard, string $key)
    {
        $value = config("passkeys.guards.{$guard}.{$key}");

        if (is_null($value)) {
            throw GuardNotConfigured::make($guard);
        }

        return $value;
    }
}

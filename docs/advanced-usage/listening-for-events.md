---
title: Listening for events
weight: 3
---

The package fires the `Spatie\LaravelPasskeys\Events\PasskeyUsedToAuthenticateEvent` when a passkey is used to authenticate. It has a property `passkey` that contains the `Passkey` model that was used to authenticate, and `request` which contains the `AuthenticateUsingPasskeysRequest`.

It also fires the `Spatie\LaravelPasskeys\Events\PasskeyRegisteredEvent` when a passkey is registered against an authenticatable model. It has a property `passkey` that contains the `Passkey` model that was used to authenticate, and `authenticatable` which contains the model that the passkey was registered against.

If you'd like to omit sending these events this can be setup in the config file under events. Setting a to `true` will enable sending events for each action.

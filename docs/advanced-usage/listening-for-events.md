---
title: Listening for events
weight: 3
---

The package fires the `Spatie\LaravelPasskeys\Events\PasskeyUsedToAuthenticateEvent` when a passkey is used to authenticate. It has a property `passkey` that contains the `Passkey` model that was used to authenticate, and `request` which contains the `AuthenticateUsingPasskeysRequest`.

The package also fires the `Spatie\LaravelPasskeys\Events\PasskeyRegisteredEvent` when a new passkey is registered. It has a property `passkey` that contains the `Passkey` model that was registered, and `authenticatable` which contains the model that the passkey was registered against.

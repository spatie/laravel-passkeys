---
title: Listening for events
weight: 3
---

The package fires the following events:
- `Spatie\LaravelPasskeys\Events\PasskeyCreatedEvent` – triggered when a passkey is created.
- `Spatie\LaravelPasskeys\Events\PasskeyUsedToAuthenticateEvent` – triggered when a passkey is used to authenticate.

Both events include a `passkey` property, which is an instance of the `Passkey` model.

The `PasskeyUsedToAuthenticateEvent` also includes a `request` property, which is an instance of `AuthenticateUsingPasskeysRequest`.


<?php

namespace Spatie\LaravelPasskeys\Livewire;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyOptionsAction;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Support\Config;
use Throwable;
use Webauthn\PublicKeyCredentialCreationOptions;

class PasskeysComponent extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    public function render(): View
    {
        return view('passkeys::livewire.passkeys', data: [
            'passkeys' => $this->currentUser()->passkeys,
        ]);
    }

    public function validatePasskeyProperties(): void
    {
        ray('validate passkey properties');

        $this->dispatch('passkeyPropertiesValidated', [
            'passkeyOptions' => json_decode($this->generatePasskeyOptions(asJson: true)),
        ]);
    }

    public function storePasskey(string $passkey): void
    {
        ray('passkey', $passkey);
        /** @var \Spatie\LaravelPasskeys\Actions\StorePasskeyAction $storePasskeyAction */
        $storePasskeyAction = Config::getAction('store_passkey', StorePasskeyAction::class);

        try {
            $storePasskeyAction->execute(
                $this->currentUser(),
                $passkey, $this->generatePasskeyOptions(),
                request()->getHost(),
                ['name' => $this->name]
            );
        } catch (Throwable $e) {
            throw $e;
            throw ValidationException::withMessages([
                'name' => 'Something went wrong generating the passkey.',
            ])->errorBag('passkeyForm');
        }

        $this->clearForm();
    }

    public function deletePasskey(int $passkeyId): void
    {
        $this->currentUser()->passkeys()->where('id', $passkeyId)->delete();
    }

    public function currentUser(): Authenticatable&HasPasskeys
    {
        /**
         * @var Authenticatable&HasPasskeys $user
         */
        $user = auth()->user();

        return $user;
    }

    protected function clearForm(): void
    {
        $this->name = '';
    }

    protected function generatePasskeyOptions(bool $asJson = false): string|PublicKeyCredentialCreationOptions
    {
        /** @var GeneratePasskeyOptionsAction $generatePassKeyOptionsAction */
        $generatePassKeyOptionsAction = Config::getAction('generate_passkey_options', GeneratePasskeyOptionsAction::class);

        return $generatePassKeyOptionsAction->execute($this->currentUser(), $asJson);
    }
}

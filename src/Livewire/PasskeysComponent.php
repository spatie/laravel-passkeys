<?php

namespace Spatie\LaravelPasskeys\Livewire;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyRegisterOptionsAction;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Support\Config;
use Throwable;

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
        $this->dispatch('passkeyPropertiesValidated', [
            'passkeyOptions' => json_decode($this->generatePasskeyOptions()),
        ]);
    }

    public function storePasskey(string $passkey): void
    {
        /** @var \Spatie\LaravelPasskeys\Actions\StorePasskeyAction $storePasskeyAction */
        $storePasskeyAction = Config::getAction('store_passkey', StorePasskeyAction::class);

        try {
            $storePasskeyAction->execute(
                $this->currentUser(),
                $passkey, $this->previouslyGeneratedPasskeyOptions(),
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
        /** @var Authenticatable&HasPasskeys $user */
        $user = auth()->user();

        return $user;
    }

    protected function clearForm(): void
    {
        $this->name = '';
    }

    protected function generatePasskeyOptions(): string
    {
        /** @var GeneratePasskeyRegisterOptionsAction $generatePassKeyOptionsAction */
        $generatePassKeyOptionsAction = Config::getAction('generate_passkey_register_options', GeneratePasskeyRegisterOptionsAction::class);

        $options = $generatePassKeyOptionsAction->execute($this->currentUser());

        session()->put('passkey-registration-options', $options);

        return $options;
    }

    protected function previouslyGeneratedPasskeyOptions(): ?string
    {
        return session()->pull('passkey-registration-options');
    }
}

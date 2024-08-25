<?php

namespace Spatie\LaravelPasskeys\Http\Components;

use Illuminate\View\Component;

class AuthenticatePasskeyComponent extends Component
{
    public function render()
    {
        return view('passkeys::livewire.authenticatePasskeysScript');
    }
}

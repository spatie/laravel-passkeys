<?php

namespace Spatie\LaravelPasskeys\Http\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AuthenticatePasskeyComponent extends Component
{
    public function render(): View
    {
        return view('passkeys::components.authenticate');
    }
}

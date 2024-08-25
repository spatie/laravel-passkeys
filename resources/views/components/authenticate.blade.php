<div>
    @include('passkeys::components.partials.authenticateScript')

    <form id="passkey-login-form" method="POST" action="{{ route('passkeys.login') }}">
        @csrf
    </form>

    @if($message = session()->get('authenticatePasskey::message'))
        <div>
            {{ $message }}
        </div>
    @endif

   <!--
    if (! browserSupportsWebAuthn()) {
    return;
    }
   -->

    <div onclick="authenticateWithPasskey()">
        @if ($slot->isEmpty())
            <div class="underline cursor-pointer">
                Authenticate using Passkey
            </div>
        @else
            {{ $slot }}
        @endif
    </div>
</div>

<div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            if (! browserSupportsWebAuthn()) {
                return;
            }

            async function authenticateWithPasskey() {
                const response = await fetch('{{ route('passkeys.authentication_options') }}')

                const options = await response.json();

                const answer = await startAuthentication(options);

                const form = document.getElementById('passkey-login-form');

                form.addEventListener('formdata', ({formData}) => {
                    formData.set('answer', JSON.stringify(answer));
                });

                form.submit();
            }

            authenticateWithPasskey();
        });
    </script>

    <form id="passkey-login-form" method="POST" action="{{ route('passkeys.login') }}">
        @csrf
    </form>

    @if($message = session()->get('authenticatePasskey::message'))
        <div>
            {{ $message }}
        </div>
    @endif
</div>

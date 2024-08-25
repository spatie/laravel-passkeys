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
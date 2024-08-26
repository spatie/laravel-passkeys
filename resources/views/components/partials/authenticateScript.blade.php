<script>
    async function authenticateWithPasskey() {
        const response = await fetch('{{ route('passkeys.authentication_options') }}')

        const options = await response.json();

        const startAuthenticationResponse = await startAuthentication(options);

        const form = document.getElementById('passkey-login-form');

        form.addEventListener('formdata', ({formData}) => {
            formData.set('start_authentication_response', JSON.stringify(startAuthenticationResponse));
        });

        form.submit();
    }


</script>

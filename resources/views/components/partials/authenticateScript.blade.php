<script>
    async function authenticateWithPasskey() {
        const response = await fetch('{{ route('passkeys.authentication_options') }}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        });

        const options = await response.json();

        const startAuthenticationResponse = await startAuthentication(options);

        const form = document.getElementById('passkey-login-form');

        form.addEventListener('formdata', ({
            formData
        }) => {
            formData.set('start_authentication_response', JSON.stringify(startAuthenticationResponse));
        });

        form.submit();
    }

    async function startAuthentication(options) {
        // TODO: Add an ability to authenticate with a passkey: Locally verify the user and get a credential.
        // Base64URL decode the challenge.
        options.challenge = base64UrlDecode(options.challenge);
        options.allowCredentials = [];

        const credential = await navigator.credentials.get({
            publicKey: options,
        });

        return credential;
    }

    function base64UrlDecode(base64UrlString) {
        // Replace '-' with '+', '_' with '/'
        let base64 = base64UrlString.replace(/-/g, '+').replace(/_/g, '/');

        // Pad with '=' to make the string length a multiple of 4
        while (base64.length % 4) {
            base64 += '=';
        }

        // Decode the base64 string to a byte array (or string, if that's your intention)
        const decodedData = atob(base64);

        // Convert the decoded string to an array buffer, Uint8Array for binary data
        const byteArray = new Uint8Array(decodedData.length);
        for (let i = 0; i < decodedData.length; i++) {
            byteArray[i] = decodedData.charCodeAt(i);
        }

        return byteArray;
    }
</script>

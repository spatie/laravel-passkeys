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

        // TODO: Add an ability to authenticate with a passkey: Verify the credential.
        const credential = {};
        credential.id = startAuthenticationResponse.id;
        credential.rawId = base64UrlEncode(startAuthenticationResponse.rawId); // Pass a Base64URL encoded ID string.
        credential.type = startAuthenticationResponse.type;

        // Base64URL encode some values.
        const clientDataJSON = base64UrlEncode(startAuthenticationResponse.response.clientDataJSON);
        const authenticatorData = base64UrlEncode(startAuthenticationResponse.response.authenticatorData);
        const signature = base64UrlEncode(startAuthenticationResponse.response.signature);
        const userHandle = base64UrlEncode(startAuthenticationResponse.response.userHandle);

        credential.response = {
            clientDataJSON,
            authenticatorData,
            signature,
            userHandle,
        };

        const form = document.getElementById('passkey-login-form');

        form.addEventListener('formdata', ({
            formData
        }) => {
            formData.set('start_authentication_response', JSON.stringify(credential));
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

    function base64UrlEncode(arrayBuffer) {
        const byteArray = new Uint8Array(arrayBuffer);
        let byteString = '';
        for (let i = 0; i < byteArray.byteLength; i++) {
            byteString += String.fromCharCode(byteArray[i]);
        }

        let base64String = btoa(byteString);

        base64String = base64String.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');

        return base64String;
    }
</script>

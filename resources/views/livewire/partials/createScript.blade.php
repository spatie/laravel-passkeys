<script>
    document.addEventListener('DOMContentLoaded', function() {
        Livewire.on('passkeyPropertiesValidated', async function(eventData) {
            const passkeyOptions = eventData.passkeyOptions;

            const passkey = await startRegistration(passkeyOptions);

            const credential = {};
            credential.id = passkey.id;
            credential.rawId = base64UrlEncode(passkey.rawId); // Pass a Base64URL encoded ID string.
            credential.type = passkey.type;

            // The authenticatorAttachment string in the PublicKeyCredential object is a new addition in WebAuthn L3.
            if (passkey.authenticatorAttachment) {
                credential.authenticatorAttachment = passkey.authenticatorAttachment;
            }

            // Base64URL encode some values.
            const clientDataJSON = base64UrlEncode(passkey.response.clientDataJSON);
            const attestationObject = base64UrlEncode(passkey.response.attestationObject);

            // Obtain transports.
            const transports = passkey.response.getTransports ?
                passkey.response.getTransports() : [];

            credential.response = {
                clientDataJSON,
                attestationObject,
                transports
            };

            @this.call('storePasskey', JSON.stringify(credential));
        });

        async function startRegistration(options) {
            // Base64URL decode the challenge.
            options.challenge = base64UrlDecode(options.challenge);
            options.user.id = base64UrlDecode(options.user.id);

            // if (options.excludeCredentials) {
                // for (let cred of options.excludeCredentials) {
                //     cred.id = base64UrlDecode(cred.id);
                // }
            // }

            // Use platform authenticator and discoverable credential.
            options.authenticatorSelection = {
                authenticatorAttachment: 'platform',
                requireResidentKey: true
            }

            const credential = await navigator.credentials.create({
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
            // Convert the array buffer to a string
            const byteArray = new Uint8Array(arrayBuffer);
            let byteString = '';
            for (let i = 0; i < byteArray.byteLength; i++) {
                byteString += String.fromCharCode(byteArray[i]);
            }

            // Encode the string to base64
            let base64String = btoa(byteString);

            // Replace '+' with '-', '/' with '_', and remove trailing '='
            base64String = base64String.replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');

            return base64String;
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        Livewire.on('passkeyPropertiesValidated', async function (eventData) {
            const passkeyOptions = eventData[0].passkeyOptions;

            const passkey = await startRegistration(passkeyOptions);

            @this.call('storePasskey', JSON.stringify(passkey));
        });
    });
</script>

<div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            async function authenticateWithPasskey() {
                const response = await fetch('{{ route('passkeys.authentication_options') }}')

                const options = await response.json();

                console.log(options)
                const answer = await startAuthentication(options);

                console.log('here comes the answer');
                console.log(answer);

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
</div>

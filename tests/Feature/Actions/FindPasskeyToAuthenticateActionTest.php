<?php

use Spatie\LaravelPasskeys\Actions\FindPasskeyToAuthenticateAction;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyAuthenticationOptionsAction;
use Spatie\LaravelPasskeys\Models\Passkey;
use Spatie\LaravelPasskeys\Support\Config;
use Webauthn\PublicKeyCredentialSource;

function base64url_encode_test(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function buildSignedPublicKeyCredentialJson(string $rawId, string $challenge, OpenSSLAsymmetricKey $privateKey, string $userHandle): string
{
    $rpIdHash = hash('sha256', parse_url(config('app.url'), PHP_URL_HOST), true);
    $flags = chr(0x05); // user present + user verified
    $signCount = pack('N', 101);
    $authenticatorData = $rpIdHash . $flags . $signCount;

    $clientDataJSON = json_encode([
        'type' => 'webauthn.get',
        'challenge' => base64url_encode_test($challenge),
        'origin' => config('app.url'),
    ]);

    $signedData = $authenticatorData . hash('sha256', $clientDataJSON, true);
    openssl_sign($signedData, $signature, $privateKey, OPENSSL_ALGO_SHA256);

    return json_encode([
        'id' => base64url_encode_test($rawId),
        'rawId' => base64url_encode_test($rawId),
        'type' => 'public-key',
        'response' => [
            'clientDataJSON' => base64url_encode_test($clientDataJSON),
            'authenticatorData' => base64url_encode_test($authenticatorData),
            'signature' => base64url_encode_test($signature),
            'userHandle' => base64url_encode_test($userHandle),
        ],
    ]);
}

function buildCoseKey(OpenSSLAsymmetricKey $key): string
{
    $details = openssl_pkey_get_details($key);

    $map = CBOR\MapObject::create();
    $map->add(CBOR\UnsignedIntegerObject::create(1), CBOR\UnsignedIntegerObject::create(2));    // kty: EC2
    $map->add(CBOR\UnsignedIntegerObject::create(3), CBOR\NegativeIntegerObject::create(-7));    // alg: ES256
    $map->add(CBOR\NegativeIntegerObject::create(-1), CBOR\UnsignedIntegerObject::create(1));    // crv: P-256
    $map->add(CBOR\NegativeIntegerObject::create(-2), CBOR\ByteStringObject::create($details['ec']['x'])); // x
    $map->add(CBOR\NegativeIntegerObject::create(-3), CBOR\ByteStringObject::create($details['ec']['y'])); // y

    return (string) $map;
}

beforeEach(function () {
    $this->action = Config::getAction('find_passkey', FindPasskeyToAuthenticateAction::class);
});

it('returns null when no passkey is found for the credential', function () {
    $publicKeyCredentialJson = json_encode([
        'id' => base64url_encode_test('nonexistent-credential-id'),
        'rawId' => base64url_encode_test('nonexistent-credential-id'),
        'type' => 'public-key',
        'response' => [
            'clientDataJSON' => base64url_encode_test(json_encode([
                'type' => 'webauthn.get',
                'challenge' => base64url_encode_test('test-challenge'),
                'origin' => config('app.url'),
            ])),
            'authenticatorData' => base64url_encode_test(str_repeat("\0", 37)),
            'signature' => base64url_encode_test('fake-signature'),
        ],
    ]);

    $passkeyOptionsJson = (new GeneratePasskeyAuthenticationOptionsAction())->execute();

    $result = $this->action->execute($publicKeyCredentialJson, $passkeyOptionsJson);

    expect($result)->toBeNull();
});

it('returns a passkey when authentication succeeds', function () {
    config()->set('app.url', 'https://localhost');

    $authenticatableModel = Config::getAuthenticatableModel();
    $user = $authenticatableModel::factory()->create();

    // Generate a real EC P-256 key pair
    $privateKey = openssl_pkey_new(['ec' => ['curve_name' => 'prime256v1']]);
    $cosePublicKey = buildCoseKey($privateKey);

    $rawId = random_bytes(32);

    $passkey = Passkey::factory()->for($user, 'authenticatable')->create([
        'data' => new PublicKeyCredentialSource(
            publicKeyCredentialId: $rawId,
            type: 'public-key',
            transports: [],
            attestationType: 'none',
            trustPath: Webauthn\TrustPath\EmptyTrustPath::create(),
            aaguid: Symfony\Component\Uid\Uuid::fromString('00000000-0000-0000-0000-000000000000'),
            credentialPublicKey: $cosePublicKey,
            userHandle: (string) $user->id,
            counter: 100,
        ),
    ]);

    // Get authentication options (stores challenge in session)
    $passkeyOptionsJson = (new GeneratePasskeyAuthenticationOptionsAction())->execute();
    $challenge = json_decode($passkeyOptionsJson, true)['challenge'];

    // Build a properly signed assertion using the real challenge
    $publicKeyCredentialJson = buildSignedPublicKeyCredentialJson(
        $rawId,
        base64_decode(strtr($challenge, '-_', '+/'), true),
        $privateKey,
        (string) $user->id,
    );

    $result = $this->action->execute($publicKeyCredentialJson, $passkeyOptionsJson);

    expect($result)
        ->toBeInstanceOf(Passkey::class)
        ->id->toBe($passkey->id);
});

<?php

namespace Spatie\LaravelPasskeys\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\LaravelPasskeys\Database\Factories\PasskeyFactory;
use Spatie\LaravelPasskeys\Support\Config;
use Spatie\LaravelPasskeys\Tests\TestSupport\Factories\UserFactory;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\Denormalizer\WebauthnSerializerFactory;
use Webauthn\PublicKeyCredentialSource;

class Passkey extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'credential_id',
        'data',
    ];

    public function data(): Attribute
    {
        return new Attribute(
            get: fn (string $value) => (new WebauthnSerializerFactory(AttestationStatementSupportManager::create()))
                ->create()
                ->deserialize($value, PublicKeyCredentialSource::class, 'json'),
            set: fn (PublicKeyCredentialSource $value) => [
                'credential_id' => $value->publicKeyCredentialId,
                'data' => json_encode($value),
            ],
        );
    }

    public function authenticatable(): BelongsTo
    {
        $authenticatableModel = Config::getAuthenticatableModel();

        return $this->belongsTo($authenticatableModel);
    }

    protected static function newFactory(): Factory
    {
        return PasskeyFactory::new();
    }
}

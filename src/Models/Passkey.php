<?php

namespace Spatie\LaravelPasskeys\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\LaravelPasskeys\Database\Factories\PasskeyFactory;
use Spatie\LaravelPasskeys\Support\Config;
use Spatie\LaravelPasskeys\Support\Serializer;
use Webauthn\PublicKeyCredentialSource;

/**
 * @property PublicKeyCredentialSource $data
 */
class Passkey extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function casts(): array
    {
        return [
            'last_used_at' => 'datetime',
        ];
    }

    public function data(): Attribute
    {
        $serializer = Serializer::make();

        return new Attribute(
            get: fn (string $value) => $serializer->fromJson(
                $value,
                PublicKeyCredentialSource::class
            ),
            set: fn (PublicKeyCredentialSource $value) => [
                'credential_id' => mb_convert_encoding($value->publicKeyCredentialId, 'UTF-8'),
                'data' => $serializer->toJson($value),
            ],
        );
    }

    /**
     * @return BelongsTo|MorphTo
     */
    public function authenticatable()
    {
        if (! Config::isMultiAuthEnabled()) {
            $authenticatableModel = Config::getAuthenticatableModel();

            return $this->belongsTo($authenticatableModel);
        }

        return $this->morphTo();
    }

    protected static function newFactory(): Factory
    {
        return PasskeyFactory::new();
    }
}

<?php

namespace Spatie\LaravelPasskeys\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
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
                PublicKeyCredentialSource::class,
            ),
            set: fn (PublicKeyCredentialSource $value) => [
                'credential_id' => self::encodeCredentialId($value->publicKeyCredentialId),
                'data' => $serializer->toJson($value),
            ],
        );
    }

    public function authenticatable(): BelongsTo
    {
        $authenticatableModel = Config::getAuthenticatableModel();

        return $this->belongsTo($authenticatableModel);
    }

    /**
     * Encode a credential id in a DB-safe way. When using PostgreSQL we need to store
     * binary credential ids as base64 so we don't insert invalid UTF-8 into text columns.
     */
    public static function encodeCredentialId(string $raw): string
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            return base64_encode($raw);
        }

        return mb_convert_encoding($raw, 'UTF-8');
    }

    protected static function newFactory(): Factory
    {
        return PasskeyFactory::new();
    }
}
